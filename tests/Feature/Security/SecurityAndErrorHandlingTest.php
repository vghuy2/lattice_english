<?php

namespace Tests\Feature\Security;

use App\Enums\ContentStatus;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Models\User;
use App\Models\VocabularyLesson;
use App\Models\VocabularyPracticeSession;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityAndErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentA;
    protected User $studentB;
    protected User $admin;
    protected WritingPrompt $prompt;
    protected VocabularyLesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('auth-attempts:127.0.0.1');

        $this->studentA = User::factory()->create([
            'role' => UserRole::STUDENT,
            'onboarding_completed_at' => now(),
        ]);

        $this->studentB = User::factory()->create([
            'role' => UserRole::STUDENT,
            'onboarding_completed_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $topic = VocabularyTopic::create([
            'title' => 'Test Topic',
            'slug' => 'test-topic',
            'status' => ContentStatus::PUBLISHED,
        ]);

        $this->lesson = VocabularyLesson::create([
            'topic_id' => $topic->id,
            'title' => 'Test Lesson',
            'slug' => 'test-lesson',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $this->prompt = WritingPrompt::create([
            'task_type' => WritingTaskType::TASK_1,
            'prompt_type' => WritingPromptType::LINE_GRAPH,
            'title' => 'Test Prompt',
            'slug' => 'test-prompt',
            'prompt_text' => 'Test prompt description',
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
        ]);
    }

    public function test_student_cannot_access_other_student_writing_submission(): void
    {
        $submission = WritingSubmission::create([
            'user_id' => $this->studentA->id,
            'writing_prompt_id' => $this->prompt->id,
            'essay_content' => 'Sample essay text from Student A...',
            'word_count' => 150,
            'status' => SubmissionStatus::GRADED,
            'overall_score' => 6.0,
        ]);

        $response = $this->actingAs($this->studentB)
            ->get(route('student.writing.submissions.show', $submission));

        $response->assertStatus(403);
    }

    public function test_student_cannot_access_other_student_practice_session(): void
    {
        $session = VocabularyPracticeSession::create([
            'user_id' => $this->studentA->id,
            'lesson_id' => $this->lesson->id,
            'session_type' => 'flashcard_quiz',
            'total_questions' => 5,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($this->studentB)
            ->get(route('student.vocabulary.practice.show', $session));

        $response->assertStatus(403);
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->studentA)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('student.dashboard'));
        $response->assertRedirect(route('login'));

        $responseAdmin = $this->get(route('admin.dashboard'));
        $responseAdmin->assertRedirect(route('login'));
    }

    public function test_non_existent_route_returns_404(): void
    {
        $response = $this->actingAs($this->studentA)->get('/student/non-existent-page-xyz');
        $response->assertStatus(404);
    }

    public function test_rate_limiter_throttles_excessive_auth_attempts(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('login.store'), [
                'email' => 'fake@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('login.store'), [
            'email' => 'fake@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }
}
