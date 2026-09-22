<?php

namespace App\Jobs;

use App\Enums\SubmissionStatus;
use App\Models\WritingSubmission;
use App\Services\Cache\RedisCacheKeys;
use App\Services\Scoring\WritingScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ScoreWritingSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 30, 60];

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $submissionId
    ) {}

    /**
     * Execute the job with distributed locking and deterministic scoring.
     */
    public function handle(WritingScoringService $scoringService): void
    {
        $lockKey = RedisCacheKeys::scoreLockKey($this->submissionId);
        $lock = Cache::lock($lockKey, RedisCacheKeys::TTL_SCORE_LOCK);

        if (! $lock->get()) {
            Log::info("ScoreWritingSubmission: Lock skipped - submission [{$this->submissionId}] is already being processed.");
            return;
        }

        try {
            Log::info("ScoreWritingSubmission: Lock acquired - scoring started for submission [{$this->submissionId}].");

            $submission = WritingSubmission::find($this->submissionId);
            if (! $submission) {
                Log::warning("ScoreWritingSubmission: Submission [{$this->submissionId}] not found.");
                return;
            }

            if ($submission->isGraded()) {
                Log::info("ScoreWritingSubmission: Submission [{$this->submissionId}] is already graded.");
                return;
            }

            if ($submission->status !== SubmissionStatus::GRADING) {
                $submission->status = SubmissionStatus::GRADING;
                $submission->save();
            }

            $scoringService->scoreSubmission($submission);

            // Clear student dashboard cache so latest score reflects on dashboard
            RedisCacheKeys::invalidateStudentDashboard($submission->user_id);

            Log::info("ScoreWritingSubmission: Scoring completed for submission [{$this->submissionId}], Overall Band: {$submission->overall_score}.");
        } catch (\Throwable $e) {
            Log::error("ScoreWritingSubmission: Scoring failed for submission [{$this->submissionId}]: " . $e->getMessage());
            throw $e;
        } finally {
            $lock->release();
        }
    }

    /**
     * Handle a job failure after all retries are exhausted.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::critical("ScoreWritingSubmission: Submission [{$this->submissionId}] permanently failed scoring: " . ($exception?->getMessage() ?? 'Unknown error'));

        $submission = WritingSubmission::find($this->submissionId);
        if ($submission) {
            RedisCacheKeys::invalidateStudentDashboard($submission->user_id);
        }
    }
}
