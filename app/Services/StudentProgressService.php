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
use App\Services\Cache\RedisCacheKeys;

class StudentProgressService
{
    /**
     * Get complete dashboard summary data for a student.
     *
     * @return array<string, mixed>
     */
    public function getDashboardData(User $user): array
    {
        $cacheKey = RedisCacheKeys::studentDashboardKey($user->id);

        $cached = RedisCacheKeys::rememberOrFallback($cacheKey, RedisCacheKeys::TTL_STUDENT_DASHBOARD, function () use ($user) {
            $vocabStats = $this->getVocabularyStats($user);
            $writingStats = $this->getWritingStats($user);
            $streakData = $this->calculateStudyStreak($user);
            $inProgressData = $this->getInProgressLearning($user);
            $bandProgress = $this->getBandProgress($user, $writingStats['avg_overall_band']);

            return [
                'vocab' => $vocabStats,
                'writing' => $writingStats,
                'streak' => $streakData,
                'in_progress' => $inProgressData,
                'band_progress' => $bandProgress,
            ];
        });

        $cached['user'] = $user;

        return $cached;
    }

    /**
     * Invalidate cached dashboard data for a student.
     */
    public function invalidateDashboardCache(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : (int) $user;
        RedisCacheKeys::invalidateStudentDashboard($userId);
    }

    /**
     * Calculate vocabulary statistics.
     *
     * @return array<string, mixed>
     */
    public function getVocabularyStats(User $user): array
    {
        $stats = StudentVocabularyReview::where('user_id', $user->id)
            ->selectRaw("
                COUNT(CASE WHEN status = ? THEN 1 END) as mastered_count,
                COUNT(CASE WHEN status = ? THEN 1 END) as learning_count,
                COUNT(CASE WHEN is_favorite = 1 THEN 1 END) as saved_count
            ", [WordStudyStatus::KNOWN->value, WordStudyStatus::LEARNING->value])
            ->first();

        $totalMastered = (int) ($stats->mastered_count ?? 0);
        $totalLearning = (int) ($stats->learning_count ?? 0);
        $totalSaved = (int) ($stats->saved_count ?? 0);

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
        $aggregate = WritingSubmission::where('writing_submissions.user_id', $user->id)
            ->whereIn('writing_submissions.status', [
                SubmissionStatus::SUBMITTED->value,
                SubmissionStatus::GRADING->value,
                SubmissionStatus::GRADED->value,
            ])
            ->join('writing_prompts', 'writing_submissions.writing_prompt_id', '=', 'writing_prompts.id')
            ->selectRaw("
                COUNT(writing_submissions.id) as total_submitted,
                COUNT(CASE WHEN writing_submissions.status = ? THEN 1 END) as graded_count,
                COUNT(CASE WHEN writing_prompts.task_type = 'task_1' THEN 1 END) as task1_count,
                COUNT(CASE WHEN writing_prompts.task_type = 'task_2' THEN 1 END) as task2_count,
                AVG(CASE WHEN writing_submissions.status = ? THEN writing_submissions.overall_score END) as avg_overall,
                AVG(CASE WHEN writing_submissions.status = ? THEN writing_submissions.ta_score END) as avg_ta,
                AVG(CASE WHEN writing_submissions.status = ? THEN writing_submissions.cc_score END) as avg_cc,
                AVG(CASE WHEN writing_submissions.status = ? THEN writing_submissions.lr_score END) as avg_lr,
                AVG(CASE WHEN writing_submissions.status = ? THEN writing_submissions.gra_score END) as avg_gra
            ", [
                SubmissionStatus::GRADED->value,
                SubmissionStatus::GRADED->value,
                SubmissionStatus::GRADED->value,
                SubmissionStatus::GRADED->value,
                SubmissionStatus::GRADED->value,
                SubmissionStatus::GRADED->value,
            ])
            ->first();

        $totalSubmissions = (int) ($aggregate->total_submitted ?? 0);
        $gradedCount = (int) ($aggregate->graded_count ?? 0);
        $task1Count = (int) ($aggregate->task1_count ?? 0);
        $task2Count = (int) ($aggregate->task2_count ?? 0);

        $avgOverall = $gradedCount > 0 && $aggregate->avg_overall !== null ? round((float) $aggregate->avg_overall, 1) : null;
        $avgTa = $gradedCount > 0 && $aggregate->avg_ta !== null ? round((float) $aggregate->avg_ta, 1) : null;
        $avgCc = $gradedCount > 0 && $aggregate->avg_cc !== null ? round((float) $aggregate->avg_cc, 1) : null;
        $avgLr = $gradedCount > 0 && $aggregate->avg_lr !== null ? round((float) $aggregate->avg_lr, 1) : null;
        $avgGra = $gradedCount > 0 && $aggregate->avg_gra !== null ? round((float) $aggregate->avg_gra, 1) : null;

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
        // Limit streak search window to 90 days for peak performance
        $cutoffDate = Carbon::now()->subDays(90)->startOfDay();

        $practiceDates = VocabularyPracticeSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', $cutoffDate)
            ->selectRaw('DISTINCT DATE(created_at) as active_date')
            ->pluck('active_date');

        $writingDates = WritingSubmission::where('user_id', $user->id)
            ->where('updated_at', '>=', $cutoffDate)
            ->selectRaw('DISTINCT DATE(updated_at) as active_date')
            ->pluck('active_date');

        $lessonDates = StudentLessonProgress::where('user_id', $user->id)
            ->where('updated_at', '>=', $cutoffDate)
            ->selectRaw('DISTINCT DATE(updated_at) as active_date')
            ->pluck('active_date');

        $uniqueDates = $practiceDates->merge($writingDates)->merge($lessonDates)
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->sortDesc()
            ->values();

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
