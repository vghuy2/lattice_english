<?php

namespace App\Services;

use App\Enums\LearningStatus;
use App\Enums\PracticeQuestionType;
use App\Enums\WordStudyStatus;
use App\Models\StudentLessonProgress;
use App\Models\StudentVocabularyReview;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyPracticeAnswer;
use App\Models\VocabularyPracticeSession;
use App\Services\Cache\RedisCacheKeys;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VocabularyLearningService
{
    /**
     * Mark study status for a word and update overall lesson progress.
     */
    public function markWordStatus(User $user, VocabularyItem $item, string $status): StudentVocabularyReview
    {
        return DB::transaction(function () use ($user, $item, $status) {
            $studyStatus = WordStudyStatus::tryFrom($status) ?? WordStudyStatus::LEARNING;

            $review = StudentVocabularyReview::firstOrNew([
                'user_id' => $user->id,
                'vocabulary_item_id' => $item->id,
            ]);

            $review->status = $studyStatus;
            $review->last_reviewed_at = now();

            if ($studyStatus === WordStudyStatus::KNOWN) {
                $review->mastery_level = max($review->mastery_level, 3);
                $review->next_review_at = now()->addDays(3)->toDateString();
            } elseif ($studyStatus === WordStudyStatus::REVIEW_NEEDED) {
                $review->mastery_level = max(1, $review->mastery_level - 1);
                $review->next_review_at = now()->toDateString();
            } else {
                $review->next_review_at = now()->addDay()->toDateString();
            }

            $review->save();

            // Recalculate lesson progress
            $this->recalculateLessonProgress($user, $item->lesson_id);

            return $review;
        });
    }

    /**
     * Toggle favorite bookmark for a word.
     */
    public function toggleFavorite(User $user, VocabularyItem $item): bool
    {
        $review = StudentVocabularyReview::firstOrCreate([
            'user_id' => $user->id,
            'vocabulary_item_id' => $item->id,
        ]);

        $review->is_favorite = ! $review->is_favorite;
        $review->save();

        return $review->is_favorite;
    }

    /**
     * Save personal learning note for a word.
     */
    public function savePersonalNote(User $user, VocabularyItem $item, ?string $note): StudentVocabularyReview
    {
        $review = StudentVocabularyReview::firstOrCreate([
            'user_id' => $user->id,
            'vocabulary_item_id' => $item->id,
        ]);

        $review->personal_note = $note;
        $review->save();

        return $review;
    }

    /**
     * Recalculate and update lesson progress for a student.
     */
    public function recalculateLessonProgress(User $user, int $lessonId): StudentLessonProgress
    {
        $lesson = VocabularyLesson::findOrFail($lessonId);
        $totalItems = $lesson->items()->count();

        $studiedItemsCount = StudentVocabularyReview::where('user_id', $user->id)
            ->whereIn('vocabulary_item_id', $lesson->items()->pluck('id'))
            ->whereIn('status', [WordStudyStatus::KNOWN->value, WordStudyStatus::LEARNING->value])
            ->count();

        $progress = StudentLessonProgress::firstOrNew([
            'user_id' => $user->id,
            'lesson_id' => $lessonId,
        ]);

        $progress->total_items_count = $totalItems;
        $progress->completed_items_count = $studiedItemsCount;
        $progress->last_studied_at = now();

        if ($totalItems > 0 && $studiedItemsCount >= $totalItems) {
            $progress->status = LearningStatus::COMPLETED;
            $progress->completed_at = $progress->completed_at ?? now();
        } elseif ($studiedItemsCount > 0) {
            $progress->status = LearningStatus::IN_PROGRESS;
        } else {
            $progress->status = LearningStatus::NOT_STARTED;
        }

        $progress->save();

        // Invalidate student dashboard cache
        RedisCacheKeys::invalidateStudentDashboard($user->id);

        return $progress;
    }

    /**
     * Generate an interactive practice quiz session for a lesson.
     */
    public function generatePracticeSession(User $user, VocabularyLesson $lesson): VocabularyPracticeSession
    {
        return DB::transaction(function () use ($user, $lesson) {
            $items = $lesson->items()->ordered()->get();
            if ($items->isEmpty()) {
                throw new \InvalidArgumentException('Bài học chưa có từ vựng để luyện tập.');
            }

            // Distractor pool
            $distractorItems = VocabularyItem::where('lesson_id', '!=', $lesson->id)
                ->inRandomOrder()
                ->limit(20)
                ->get();

            if ($distractorItems->count() < 4) {
                $distractorItems = $items;
            }

            $session = VocabularyPracticeSession::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'session_type' => 'lesson_practice',
                'total_questions' => $items->count(),
                'correct_answers' => 0,
                'accuracy_rate' => 0.00,
                'status' => 'in_progress',
            ]);

            $types = [
                PracticeQuestionType::MULTIPLE_CHOICE,
                PracticeQuestionType::MEANING_SELECT,
                PracticeQuestionType::FILL_IN_BLANK,
                PracticeQuestionType::CONTEXTUAL,
            ];

            $answersData = [];
            $now = now();

            foreach ($items as $index => $item) {
                $questionType = $types[$index % count($types)];
                $questionData = $this->buildQuestionPayload($item, $questionType, $items, $distractorItems);

                $answersData[] = [
                    'session_id' => $session->id,
                    'vocabulary_item_id' => $item->id,
                    'question_type' => $questionType->value,
                    'question_data' => json_encode($questionData),
                    'user_answer' => null,
                    'is_correct' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            VocabularyPracticeAnswer::insert($answersData);

            return $session;
        });
    }

    /**
     * Generate a quick review quiz from due words.
     */
    public function generateReviewSession(User $user, int $limit = 10): VocabularyPracticeSession
    {
        return DB::transaction(function () use ($user, $limit) {
            $reviewItemIds = StudentVocabularyReview::where('user_id', $user->id)
                ->dueForReview()
                ->inRandomOrder()
                ->limit($limit)
                ->pluck('vocabulary_item_id');

            if ($reviewItemIds->isEmpty()) {
                // Fallback to any studied items
                $reviewItemIds = StudentVocabularyReview::where('user_id', $user->id)
                    ->inRandomOrder()
                    ->limit($limit)
                    ->pluck('vocabulary_item_id');
            }

            $items = VocabularyItem::whereIn('id', $reviewItemIds)->get();
            if ($items->isEmpty()) {
                throw new \InvalidArgumentException('Bạn chưa có từ vựng nào trong danh sách ôn tập.');
            }

            $distractors = VocabularyItem::whereNotIn('id', $items->pluck('id'))->inRandomOrder()->limit(15)->get();
            if ($distractors->count() < 4) {
                $distractors = $items;
            }

            $session = VocabularyPracticeSession::create([
                'user_id' => $user->id,
                'lesson_id' => null,
                'session_type' => 'review_quiz',
                'total_questions' => $items->count(),
                'correct_answers' => 0,
                'accuracy_rate' => 0.00,
                'status' => 'in_progress',
            ]);

            $types = [
                PracticeQuestionType::MEANING_SELECT,
                PracticeQuestionType::FILL_IN_BLANK,
                PracticeQuestionType::MULTIPLE_CHOICE,
                PracticeQuestionType::CONTEXTUAL,
            ];

            $answersData = [];
            $now = now();

            foreach ($items as $idx => $item) {
                $questionType = $types[$idx % count($types)];
                $questionData = $this->buildQuestionPayload($item, $questionType, $items, $distractors);

                $answersData[] = [
                    'session_id' => $session->id,
                    'vocabulary_item_id' => $item->id,
                    'question_type' => $questionType->value,
                    'question_data' => json_encode($questionData),
                    'user_answer' => null,
                    'is_correct' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            VocabularyPracticeAnswer::insert($answersData);

            return $session;
        });
    }

    /**
     * Build structured payload for a question type.
     */
    protected function buildQuestionPayload(VocabularyItem $item, PracticeQuestionType $type, $lessonItems, $distractors): array
    {
        $pool = $lessonItems->merge($distractors)->unique('id')->where('id', '!=', $item->id)->values();

        return match ($type) {
            PracticeQuestionType::MULTIPLE_CHOICE => [
                'prompt' => 'Chọn từ hoặc cụm từ phù hợp nhất để hoàn thành câu văn học thuật sau:',
                'sentence' => str_ireplace($item->word, '__________', $item->example_sentence),
                'hint_vi' => $item->example_sentence_vi,
                'options' => $this->shuffleOptions($item->word, $pool->pluck('word')->toArray()),
                'correct_answer' => $item->word,
                'explanation' => "Từ \"{$item->word}\" ({$item->part_of_speech->short()}) mang nghĩa: {$item->vietnamese_meaning}.",
            ],
            PracticeQuestionType::MEANING_SELECT => [
                'prompt' => "Chọn nghĩa Tiếng Việt chính xác của từ \"{$item->word}\" ({$item->part_of_speech->short()}):",
                'word' => $item->word,
                'ipa' => $item->ipa,
                'options' => $this->shuffleOptions($item->vietnamese_meaning, $pool->pluck('vietnamese_meaning')->toArray()),
                'correct_answer' => $item->vietnamese_meaning,
                'explanation' => "\"{$item->word}\" nghĩa là: {$item->vietnamese_meaning}. Ví dụ: \"{$item->example_sentence}\".",
            ],
            PracticeQuestionType::FILL_IN_BLANK => [
                'prompt' => 'Nhập từ thích hợp vào chỗ trống (dựa trên gợi ý nghĩa Tiếng Việt):',
                'sentence' => str_ireplace($item->word, '__________', $item->example_sentence),
                'clue' => "Gợi ý: {$item->vietnamese_meaning} ({$item->part_of_speech->short()})",
                'correct_answer' => $item->word,
                'explanation' => "Đáp án đúng là \"{$item->word}\". Câu hoàn chỉnh: \"{$item->example_sentence}\".",
            ],
            PracticeQuestionType::CONTEXTUAL => [
                'prompt' => "Trong bài thi IELTS Writing, từ \"{$item->word}\" được sử dụng chính xác trong trường hợp nào?",
                'word' => $item->word,
                'options' => [
                    $item->example_sentence,
                    "It is unnecessary to consider {$item->word} in casual daily chats.",
                    "The word is only used in spoken dialogues, not academic essays.",
                    "None of the above contexts are appropriate.",
                ],
                'correct_answer' => $item->example_sentence,
                'explanation' => "Ngữ cảnh chuẩn: \"{$item->example_sentence}\". Ghi chú: {$item->writing_notes}",
            ],
            default => [
                'prompt' => "Chọn đáp án đúng cho \"{$item->word}\":",
                'options' => [$item->vietnamese_meaning, 'Phương án B', 'Phương án C', 'Phương án D'],
                'correct_answer' => $item->vietnamese_meaning,
                'explanation' => "Nghĩa đúng là: {$item->vietnamese_meaning}",
            ],
        };
    }

    /**
     * Shuffle 1 correct answer with up to 3 distractors.
     */
    protected function shuffleOptions(string $correct, array $distractorPool): array
    {
        $filtered = array_values(array_unique(array_filter($distractorPool, fn ($val) => !empty($val) && $val !== $correct)));
        shuffle($filtered);

        $selected = array_slice($filtered, 0, 3);
        $options = array_merge([$correct], $selected);
        shuffle($options);

        return $options;
    }

    /**
     * Submit and evaluate practice quiz session.
     */
    public function submitPracticeSession(VocabularyPracticeSession $session, array $userAnswers): VocabularyPracticeSession
    {
        return DB::transaction(function () use ($session, $userAnswers) {
            if ($session->isCompleted()) {
                return $session; // Prevent double submission
            }

            $answers = $session->answers()->with('vocabularyItem')->get();
            $correctCount = 0;

            foreach ($answers as $answer) {
                $userAns = $userAnswers[$answer->id] ?? null;
                $isCorrect = false;

                if ($userAns !== null) {
                    $correctAnswer = $answer->question_data['correct_answer'] ?? '';

                    if ($answer->question_type === PracticeQuestionType::FILL_IN_BLANK) {
                        // Case-insensitive trimmed match
                        $isCorrect = (trim(mb_strtolower($userAns)) === trim(mb_strtolower($correctAnswer)));
                    } else {
                        $isCorrect = (trim((string) $userAns) === trim((string) $correctAnswer));
                    }
                }

                $answer->user_answer = is_array($userAns) ? json_encode($userAns) : (string) $userAns;
                $answer->is_correct = $isCorrect;
                $answer->save();

                if ($isCorrect) {
                    $correctCount++;
                }

                // Update spaced review progress for this word
                $this->updateWordReviewOnPractice($session->user_id, $answer->vocabulary_item_id, $isCorrect);
            }

            $total = $answers->count();
            $accuracy = $total > 0 ? round(($correctCount / $total) * 100, 2) : 0.00;

            $session->update([
                'correct_answers' => $correctCount,
                'accuracy_rate' => $accuracy,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            if ($session->lesson_id) {
                RedisCacheKeys::invalidateActivePractice($session->user_id, $session->lesson_id);
            }
            RedisCacheKeys::invalidateStudentDashboard($session->user_id);

            return $session;
        });
    }

    /**
     * Update mastery level and next review date after a quiz answer.
     */
    protected function updateWordReviewOnPractice(int $userId, int $itemId, bool $isCorrect): void
    {
        $review = StudentVocabularyReview::firstOrNew([
            'user_id' => $userId,
            'vocabulary_item_id' => $itemId,
        ]);

        $review->review_count++;
        $review->last_reviewed_at = now();

        if ($isCorrect) {
            $review->correct_count++;
            $review->mastery_level = min(5, $review->mastery_level + 1);

            // Spaced interval based on mastery level (1 -> 1 day, 2 -> 3 days, 3 -> 5 days, 4 -> 7 days, 5 -> 14 days)
            $intervalDays = match ($review->mastery_level) {
                1 => 1,
                2 => 3,
                3 => 5,
                4 => 7,
                default => 14,
            };

            $review->status = ($review->mastery_level >= 4) ? WordStudyStatus::KNOWN : WordStudyStatus::LEARNING;
            $review->next_review_at = now()->addDays($intervalDays)->toDateString();
        } else {
            $review->incorrect_count++;
            $review->mastery_level = max(1, $review->mastery_level - 1);
            $review->status = WordStudyStatus::REVIEW_NEEDED;
            $review->next_review_at = now()->toDateString(); // Review immediately/today
        }

        $review->save();
    }
}
