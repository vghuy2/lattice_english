<?php

namespace App\Services;

use App\Enums\LearningStatus;
use App\Enums\SubmissionStatus;
use App\Enums\WordStudyStatus;
use App\Models\StudentLessonProgress;
use App\Models\StudentVocabularyReview;
use App\Models\User;
use App\Models\VocabularyLesson;
use App\Models\VocabularyPracticeSession;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class StudentProgressService
{
    /**
     * Get complete dashboard summary data for a student.
     *
     * @return array<string, mixed>
     */
    public function getDashboardData(User $user): array
    {
        $vocabStats = $this->getVocabularyStats($user);
        $writingStats = $this->getWritingStats($user);
        $streakData = $this->calculateStudyStreak($user);
        $inProgressData = $this->getInProgressLearning($user);
        $bandProgress = $this->getBandProgress($user, $writingStats['avg_overall_band']);

        return [
            'user' => $user,
            'vocab' => $vocabStats,
            'writing' => $writingStats,
            'streak' => $streakData,
            'in_progress' => $inProgressData,
            'band_progress' => $bandProgress,
        ];
    }

    /**
     * Calculate vocabulary statistics.
     *
     * @return array<string, mixed>
     */
    public function getVocabularyStats(User $user): array
    {
        $reviews = StudentVocabularyReview::where('user_id', $user->id)->get();
        $totalMastered = $reviews->where('status', WordStudyStatus::KNOWN)->count();
        $totalLearning = $reviews->where('status', WordStudyStatus::LEARNING)->count();
        $totalSaved = $reviews->where('is_favorite', true)->count();

        $dueReviewCount = StudentVocabularyReview::forUser($user->id)->dueForReview()->count();

        $totalLessons = VocabularyLesson::published()->count();
        $completedLessons = StudentLessonProgress::where('user_id', $user->id)
            ->where('status', LearningStatus::COMPLETED->value)
            ->count();

        $progressPercent = $totalLessons > 0 ? min(100, round(($completedLessons / $totalLessons) * 100)) : 0;

        return [
            'total_learned' => $totalMastered + $totalLearning,
            'mastered_count' => $totalMastered,
            'learning_count' => $totalLearning,
            'saved_count' => $totalSaved,
            'due_review_count' => $dueReviewCount,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'progress_percent' => $progressPercent,
        ];
    }

    /**
     * Calculate writing statistics.
     *
     * @return array<string, mixed>
     */
    public function getWritingStats(User $user): array
    {
        $submissions = WritingSubmission::where('user_id', $user->id)->submitted()->get();
        $totalSubmissions = $submissions->count();

        $gradedSubmissions = $submissions->where('status', SubmissionStatus::GRADED);
        $gradedCount = $gradedSubmissions->count();

        $avgOverall = $gradedCount > 0 ? round($gradedSubmissions->avg('overall_score'), 1) : null;
        $avgTa = $gradedCount > 0 ? round($gradedSubmissions->avg('ta_score'), 1) : null;
        $avgCc = $gradedCount > 0 ? round($gradedSubmissions->avg('cc_score'), 1) : null;
        $avgLr = $gradedCount > 0 ? round($gradedSubmissions->avg('lr_score'), 1) : null;
        $avgGra = $gradedCount > 0 ? round($gradedSubmissions->avg('gra_score'), 1) : null;

        $task1Count = WritingSubmission::where('user_id', $user->id)
            ->whereHas('prompt', fn ($q) => $q->where('task_type', 'task_1'))
            ->submitted()
            ->count();

        $task2Count = WritingSubmission::where('user_id', $user->id)
            ->whereHas('prompt', fn ($q) => $q->where('task_type', 'task_2'))
            ->submitted()
            ->count();

        $recentSubmissions = WritingSubmission::where('user_id', $user->id)
            ->with(['prompt.topic'])
            ->latest()
            ->take(4)
            ->get();

        return [
            'total_submitted' => $totalSubmissions,
            'graded_count' => $gradedCount,
            'task1_count' => $task1Count,
            'task2_count' => $task2Count,
            'avg_overall_band' => $avgOverall,
            'avg_ta' => $avgTa,
            'avg_cc' => $avgCc,
            'avg_lr' => $avgLr,
            'avg_gra' => $avgGra,
            'recent_submissions' => $recentSubmissions,
        ];
    }

    /**
     * Calculate consecutive study streak days and current week activity.
     *
     * @return array{current_streak: int, weekly_activity: array<string, bool>, active_days_count: int}
     */
    public function calculateStudyStreak(User $user): array
    {
        // Fetch active dates from practice answers, lesson progress and writing submissions
        $dates = collect();

        $dates = $dates->merge(
            VocabularyPracticeSession::where('user_id', $user->id)
                ->where('status', 'completed')
                ->pluck('created_at')
                ->map(fn ($d) => $d->toDateString())
        );

        $dates = $dates->merge(
            WritingSubmission::where('user_id', $user->id)
                ->pluck('updated_at')
                ->map(fn ($d) => $d->toDateString())
        );

        $dates = $dates->merge(
            StudentLessonProgress::where('user_id', $user->id)
                ->pluck('updated_at')
                ->map(fn ($d) => $d->toDateString())
        );

        $uniqueDates = $dates->unique()->sortDesc()->values();

        // Calculate consecutive streak starting from today or yesterday
        $currentStreak = 0;
        $today = Carbon::today();
        $checkDate = $today->copy();

        if (! $uniqueDates->contains($checkDate->toDateString())) {
            // Check yesterday
            $checkDate->subDay();
        }

        while ($uniqueDates->contains($checkDate->toDateString())) {
            $currentStreak++;
            $checkDate->subDay();
        }

        // Weekly Activity (Mon - Sun of current week)
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $period = CarbonPeriod::create($startOfWeek, $endOfWeek);

        $weeklyActivity = [];
        foreach ($period as $day) {
            $dayKey = $day->locale('vi')->isoFormat('dd');
            $weeklyActivity[$day->toDateString()] = [
                'day_name' => $dayKey,
                'is_today' => $day->isToday(),
                'is_active' => $uniqueDates->contains($day->toDateString()),
            ];
        }

        return [
            'current_streak' => $currentStreak,
            'weekly_activity' => $weeklyActivity,
            'active_days_count' => $uniqueDates->count(),
        ];
    }

    /**
     * Get in-progress lesson or draft for quick resume.
     *
     * @return array{lesson: ?VocabularyLesson, draft: ?WritingSubmission}
     */
    public function getInProgressLearning(User $user): array
    {
        $inProgressLesson = null;
        $latestProgress = StudentLessonProgress::where('user_id', $user->id)
            ->where('status', LearningStatus::IN_PROGRESS->value)
            ->with('lesson.topic')
            ->latest('updated_at')
            ->first();

        if ($latestProgress) {
            $inProgressLesson = $latestProgress->lesson;
        } else {
            // Suggest first incomplete published lesson
            $completedLessonIds = StudentLessonProgress::where('user_id', $user->id)
                ->where('status', LearningStatus::COMPLETED->value)
                ->pluck('lesson_id');

            $inProgressLesson = VocabularyLesson::published()
                ->whereNotIn('id', $completedLessonIds)
                ->with('topic')
                ->ordered()
                ->first();
        }

        $latestDraft = WritingSubmission::where('user_id', $user->id)
            ->where('status', SubmissionStatus::DRAFT->value)
            ->with('prompt')
            ->latest('updated_at')
            ->first();

        return [
            'lesson' => $inProgressLesson,
            'draft' => $latestDraft,
        ];
    }

    /**
     * Compute Band target tracking and progress percentage.
     *
     * @return array{current_band: float, target_band: float, estimated_band: float, progress_percent: int}
     */
    public function getBandProgress(User $user, ?float $avgWritingBand): array
    {
        $currentBand = (float) ($user->current_band ?? 4.5);
        $targetBand = (float) ($user->target_band ?? 6.0);

        // Estimated band is either avg writing band or current band
        $estimatedBand = $avgWritingBand ?? $currentBand;

        $gap = max(0.1, $targetBand - $currentBand);
        $achieved = max(0.0, $estimatedBand - $currentBand);
        $percent = min(100, (int) round(($achieved / $gap) * 100));

        return [
            'current_band' => $currentBand,
            'target_band' => $targetBand,
            'estimated_band' => $estimatedBand,
            'progress_percent' => $percent,
        ];
    }
}
