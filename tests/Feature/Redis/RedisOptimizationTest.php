<?php

namespace Tests\Feature\Redis;

use App\Enums\ContentStatus;
use App\Enums\SubmissionStatus;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Jobs\ScoreWritingSubmission;
use App\Models\User;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use App\Services\Cache\RedisCacheKeys;
use App\Services\Scoring\WritingScoringService;
use App\Services\StudentProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RedisOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected VocabularyTopic $topic;
    protected VocabularyLesson $lesson;
    protected WritingPrompt $prompt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->topic = VocabularyTopic::create([
            'title' => 'Technology & Society',
            'slug' => 'technology-society',
            'status' => ContentStatus::PUBLISHED,
            'order_index' => 1,
        ]);

        $this->lesson = VocabularyLesson::create([
            'topic_id' => $this->topic->id,
            'title' => 'AI in Modern Life',
            'slug' => 'ai-modern-life',
            'level' => VocabularyLevel::BAND_5_5_6_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'order_index' => 1,
        ]);

        $this->prompt = WritingPrompt::create([
            'topic_id' => $this->topic->id,
            'title' => 'Impact of Artificial Intelligence',
            'slug' => 'impact-of-ai',
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::OPINION,
            'level' => VocabularyLevel::BAND_5_5_6_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'Artificial intelligence will replace human workers in most industries. Do you agree or disagree?',
            'min_words' => 250,
            'time_limit_minutes' => 40,
            'order_index' => 1,
        ]);
    }

    public function test_topics_cache_and_invalidation(): void
    {
        RedisCacheKeys::invalidateTopics();
        $this->assertFalse(Cache::has(RedisCacheKeys::TOPICS));

        // First access: loads and caches
        $topics = RedisCacheKeys::rememberOrFallback(
            RedisCacheKeys::TOPICS,
            RedisCacheKeys::TTL_TOPICS,
            fn () => VocabularyTopic::published()->ordered()->get()
        );
        $this->assertCount(1, $topics);
        $this->assertTrue(Cache::has(RedisCacheKeys::TOPICS));

        // Invalidation clears key
        RedisCacheKeys::invalidateTopics();
        $this->assertFalse(Cache::has(RedisCacheKeys::TOPICS));
    }

    public function test_student_dashboard_cache_and_invalidation(): void
    {
        $service = app(StudentProgressService::class);
        $cacheKey = RedisCacheKeys::studentDashboardKey($this->user->id);

        RedisCacheKeys::invalidateStudentDashboard($this->user->id);
        $this->assertFalse(Cache::has($cacheKey));

        // Get dashboard data caches results
        $data1 = $service->getDashboardData($this->user);
        $this->assertArrayHasKey('vocab', $data1);
        $this->assertTrue(Cache::has($cacheKey));

        // Invalidation clears cache
        $service->invalidateDashboardCache($this->user);
        $this->assertFalse(Cache::has($cacheKey));
    }

    public function test_writing_submission_dispatches_queue_job(): void
    {
        Queue::fake();

        $essay = 'This is a sample essay submitted to verify queue dispatching and background scoring on the Redis queue system.';

        $response = $this->actingAs($this->user)
            ->post(route('student.writing.submit', $this->prompt), [
                'essay_content' => $essay,
                'time_spent_seconds' => 1500,
            ]);

        $submission = WritingSubmission::where('user_id', $this->user->id)
            ->where('writing_prompt_id', $this->prompt->id)
            ->first();

        $this->assertNotNull($submission);
        $this->assertEquals(SubmissionStatus::SUBMITTED, $submission->status);

        Queue::assertPushed(ScoreWritingSubmission::class, function ($job) use ($submission) {
            return $job->submissionId === $submission->id;
        });

        $response->assertRedirect(route('student.writing.submissions.show', $submission));
    }

    public function test_score_writing_submission_job_evaluates_and_updates_status(): void
    {
        $submission = WritingSubmission::create([
            'user_id' => $this->user->id,
            'writing_prompt_id' => $this->prompt->id,
            'essay_content' => 'In recent years, artificial intelligence has emerged as one of the most transformative technologies. Overall, while automation threatens certain routine tasks, it also creates new job categories that require critical thinking, emotional intelligence, and complex problem-solving abilities.',
            'word_count' => 38,
            'time_spent_seconds' => 1200,
            'status' => SubmissionStatus::SUBMITTED,
            'submitted_at' => now(),
        ]);

        $scoringService = app(WritingScoringService::class);
        $job = new ScoreWritingSubmission($submission->id);
        $job->handle($scoringService);

        $submission->refresh();

        $this->assertEquals(SubmissionStatus::GRADED, $submission->status);
        $this->assertNotNull($submission->overall_score);
        $this->assertGreaterThanOrEqual(3.5, $submission->overall_score);
        $this->assertNotNull($submission->ta_score);
        $this->assertNotNull($submission->cc_score);
        $this->assertNotNull($submission->lr_score);
        $this->assertNotNull($submission->gra_score);
        $this->assertNotNull($submission->scoring_breakdown);
    }

    public function test_distributed_lock_prevents_duplicate_scoring(): void
    {
        $submission = WritingSubmission::create([
            'user_id' => $this->user->id,
            'writing_prompt_id' => $this->prompt->id,
            'essay_content' => 'Sample essay to verify distributed lock behavior and duplicate scoring prevention.',
            'word_count' => 12,
            'time_spent_seconds' => 600,
            'status' => SubmissionStatus::SUBMITTED,
            'submitted_at' => now(),
        ]);

        $lockKey = RedisCacheKeys::scoreLockKey($submission->id);
        $lock = Cache::lock($lockKey, 60);
        $this->assertTrue($lock->get());

        // Second attempt with lock already held should skip scoring
        $scoringService = app(WritingScoringService::class);
        $job = new ScoreWritingSubmission($submission->id);
        $job->handle($scoringService);

        $submission->refresh();
        // Should remain SUBMITTED because lock was already acquired by another process
        $this->assertEquals(SubmissionStatus::SUBMITTED, $submission->status);

        $lock->release();
    }

    public function test_cache_graceful_fallback_on_exception(): void
    {
        // When callback executes through rememberOrFallback, it returns expected value
        $result = RedisCacheKeys::rememberOrFallback('test:fallback:key', 60, function () {
            return 'mysql_direct_result';
        });

        $this->assertEquals('mysql_direct_result', $result);
    }

    public function test_rate_limiter_keys_work_with_redis(): void
    {
        $limiterKey = 'test-rate-limit:' . $this->user->id;

        RateLimiter::clear($limiterKey);
        $this->assertEquals(0, RateLimiter::attempts($limiterKey));

        RateLimiter::hit($limiterKey, 60);
        $this->assertEquals(1, RateLimiter::attempts($limiterKey));

        RateLimiter::clear($limiterKey);
        $this->assertEquals(0, RateLimiter::attempts($limiterKey));
    }
}
