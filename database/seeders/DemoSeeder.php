<?php

namespace Database\Seeders;

use App\Enums\LearningStatus;
use App\Enums\PracticeQuestionType;
use App\Enums\SubmissionStatus;
use App\Enums\WordStudyStatus;
use App\Enums\WritingTaskType;
use App\Models\StudentLessonProgress;
use App\Models\StudentVocabularyReview;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyPracticeAnswer;
use App\Models\VocabularyPracticeSession;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use App\Services\Scoring\WritingScoringService;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run demo data seeding for student@ielts.vn.
     */
    public function run(): void
    {
        $student = User::where('email', 'student@ielts.vn')->first();
        if (! $student) {
            return;
        }

        $scoringService = app(WritingScoringService::class);

        // 1. Seed Vocabulary Reviews (Spaced Repetition Queue & Favorites)
        $items = VocabularyItem::all();
        if ($items->isNotEmpty()) {
            foreach ($items as $index => $item) {
                if ($index === 0 || $index === 1) {
                    // Due for review today
                    StudentVocabularyReview::updateOrCreate(
                        ['user_id' => $student->id, 'vocabulary_item_id' => $item->id],
                        [
                            'status' => WordStudyStatus::REVIEW_NEEDED,
                            'mastery_level' => 2,
                            'is_favorite' => true,
                            'review_count' => 3,
                            'correct_count' => 2,
                            'incorrect_count' => 1,
                            'last_reviewed_at' => now()->subDays(3),
                            'next_review_at' => now()->subDay(),
                        ]
                    );
                } elseif ($index === 2 || $index === 3) {
                    // Known / Mastered
                    StudentVocabularyReview::updateOrCreate(
                        ['user_id' => $student->id, 'vocabulary_item_id' => $item->id],
                        [
                            'status' => WordStudyStatus::KNOWN,
                            'mastery_level' => 5,
                            'is_favorite' => false,
                            'review_count' => 5,
                            'correct_count' => 5,
                            'incorrect_count' => 0,
                            'last_reviewed_at' => now()->subDay(),
                            'next_review_at' => now()->addDays(7),
                        ]
                    );
                } else {
                    // Learning
                    StudentVocabularyReview::updateOrCreate(
                        ['user_id' => $student->id, 'vocabulary_item_id' => $item->id],
                        [
                            'status' => WordStudyStatus::LEARNING,
                            'mastery_level' => 1,
                            'is_favorite' => true,
                            'review_count' => 1,
                            'correct_count' => 1,
                            'incorrect_count' => 0,
                            'last_reviewed_at' => now()->subHours(5),
                            'next_review_at' => now()->addDays(2),
                        ]
                    );
                }
            }
        }

        // 2. Seed Lesson Progress
        $lessons = VocabularyLesson::all();
        if ($lessons->isNotEmpty()) {
            // First lesson completed
            StudentLessonProgress::updateOrCreate(
                ['user_id' => $student->id, 'lesson_id' => $lessons[0]->id],
                [
                    'status' => LearningStatus::COMPLETED,
                    'completed_items_count' => 4,
                    'total_items_count' => 4,
                    'last_studied_at' => now()->subDays(1),
                    'completed_at' => now()->subDays(1),
                ]
            );

            if ($lessons->count() > 1) {
                // Second lesson in progress
                StudentLessonProgress::updateOrCreate(
                    ['user_id' => $student->id, 'lesson_id' => $lessons[1]->id],
                    [
                        'status' => LearningStatus::IN_PROGRESS,
                        'completed_items_count' => 1,
                        'total_items_count' => 2,
                        'last_studied_at' => now()->subHours(2),
                    ]
                );
            }
        }

        // 3. Seed Practice Quiz Session & Answers
        if ($lessons->isNotEmpty()) {
            $session = VocabularyPracticeSession::updateOrCreate(
                ['user_id' => $student->id, 'lesson_id' => $lessons[0]->id],
                [
                    'session_type' => 'flashcard_quiz',
                    'total_questions' => 4,
                    'correct_answers' => 3,
                    'accuracy_rate' => 75.0,
                    'status' => 'completed',
                    'completed_at' => now()->subDays(1),
                ]
            );

            foreach ($items->take(4) as $qIdx => $qItem) {
                VocabularyPracticeAnswer::updateOrCreate(
                    ['session_id' => $session->id, 'vocabulary_item_id' => $qItem->id],
                    [
                        'question_type' => PracticeQuestionType::MULTIPLE_CHOICE,
                        'question_data' => [
                            'prompt' => "Chọn nghĩa đúng của từ '{$qItem->word}'",
                            'options' => [
                                $qItem->vietnamese_meaning,
                                'lựa chọn không chính xác A',
                                'lựa chọn không chính xác B',
                                'lựa chọn không chính xác C',
                            ],
                            'correct_answer' => $qItem->vietnamese_meaning,
                        ],
                        'user_answer' => $qIdx === 3 ? 'lựa chọn không chính xác A' : $qItem->vietnamese_meaning,
                        'is_correct' => $qIdx !== 3,
                    ]
                );
            }
        }

        // 4. Seed Writing Submissions (1 Task 1 Graded, 1 Task 2 Graded, 1 Draft)
        $prompts = WritingPrompt::all();
        if ($prompts->isNotEmpty()) {
            $task1Prompt = $prompts->firstWhere('task_type', WritingTaskType::TASK_1) ?? $prompts->first();
            $task2Prompt = $prompts->firstWhere('task_type', WritingTaskType::TASK_2) ?? $prompts->last();

            if ($task1Prompt) {
                $t1Essay = "The bar chart illustrates the percentage of individuals using the internet across four different countries between the years 2010 and 2020.\n\nOverall, it is clear that there was a significant upward trend in internet usage in all surveyed countries throughout the entire decade. In addition, country A maintained the highest proportion of users in both examined years, while country B started from the lowest point.\n\nIn 2010, approximately 40% of people in country A accessed the internet on a regular basis. Subsequently, this figure increased substantially to reach 85% by the end of 2020. Similarly, country C experienced steady growth, starting from 30% and rising to 65% over the same ten-year period.\n\nHowever, country B began at a much lower percentage of only 20% in 2010, although it also achieved noticeable progress to reach nearly 50% in 2020. In conclusion, internet adoption expanded dramatically across all nations, reflecting widespread technological modernization.";

                $sub1 = WritingSubmission::updateOrCreate(
                    ['user_id' => $student->id, 'writing_prompt_id' => $task1Prompt->id],
                    [
                        'essay_content' => $t1Essay,
                        'word_count' => 164,
                        'time_spent_seconds' => 1140,
                        'status' => SubmissionStatus::SUBMITTED,
                        'submitted_at' => now()->subDays(2),
                    ]
                );

                $scoringService->scoreSubmission($sub1);
            }

            if ($task2Prompt) {
                $t2Essay = "In contemporary society, online education has transformed the way people acquire knowledge across the world.\n\nFirstly, digital learning offers remarkable flexibility. Students can access educational resources anytime, which allows them to balance study and work effectively. In addition, learners can choose courses from prestigious international universities without geographical constraints.\n\nHowever, traditional classrooms remain indispensable because direct human interaction enhances communication skills. Furthermore, group discussions in physical classrooms foster teamwork and emotional intelligence.\n\nIn conclusion, while online learning is highly beneficial, blending online and traditional education is the most effective approach for future learners.";

                $sub2 = WritingSubmission::updateOrCreate(
                    ['user_id' => $student->id, 'writing_prompt_id' => $task2Prompt->id],
                    [
                        'essay_content' => $t2Essay,
                        'word_count' => 105,
                        'time_spent_seconds' => 1800,
                        'status' => SubmissionStatus::SUBMITTED,
                        'submitted_at' => now()->subDay(),
                    ]
                );

                $scoringService->scoreSubmission($sub2);
            }

            // Seed 1 In-progress Draft
            if ($prompts->count() > 2) {
                $draftPrompt = $prompts->values()[2];
                WritingSubmission::updateOrCreate(
                    ['user_id' => $student->id, 'writing_prompt_id' => $draftPrompt->id],
                    [
                        'essay_content' => 'Nowadays, environmental pollution is becoming a severe crisis worldwide. Many experts argue that governments should invest in renewable energy sources...',
                        'word_count' => 24,
                        'time_spent_seconds' => 300,
                        'status' => SubmissionStatus::DRAFT,
                    ]
                );
            }
        }
    }
}
