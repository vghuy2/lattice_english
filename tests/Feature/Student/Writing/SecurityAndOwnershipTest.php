<?php

namespace Tests\Feature\Student\Writing;

use App\Enums\ContentStatus;
use App\Enums\SubmissionStatus;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Models\User;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndOwnershipTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentA;
    protected User $studentB;
    protected WritingPrompt $prompt;
    protected WritingSubmission $submissionA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentA = User::factory()->create();
        $this->studentB = User::factory()->create();

        $topic = VocabularyTopic::create([
            'title' => 'Globalisation & Culture',
            'slug' => 'globalisation-culture',
            'status' => ContentStatus::PUBLISHED,
            'order_index' => 1,
        ]);

        $this->prompt = WritingPrompt::create([
            'topic_id' => $topic->id,
            'title' => 'Essay: Traditional Festivals',
            'slug' => 'traditional-festivals',
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::OPINION,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'Traditional festivals are disappearing. Should governments preserve them?',
            'min_words' => 250,
            'time_limit_minutes' => 40,
            'order_index' => 1,
        ]);

        $this->submissionA = WritingSubmission::create([
            'user_id' => $this->studentA->id,
            'writing_prompt_id' => $this->prompt->id,
            'essay_content' => 'Student A private essay content about cultural preservation in modern times.',
            'word_count' => 10,
            'time_spent_seconds' => 900,
            'status' => SubmissionStatus::SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function test_guest_cannot_access_student_writing_routes(): void
    {
        $this->get(route('student.writing.index'))->assertRedirect(route('login'));
        $this->get(route('student.writing.submissions.index'))->assertRedirect(route('login'));
        $this->get(route('student.writing.practice', $this->prompt))->assertRedirect(route('login'));
        $this->get(route('student.writing.submissions.show', $this->submissionA))->assertRedirect(route('login'));
    }

    public function test_student_cannot_view_another_students_submission(): void
    {
        $response = $this->actingAs($this->studentB)
            ->get(route('student.writing.submissions.show', $this->submissionA));

        $response->assertForbidden();
    }

    public function test_submissions_list_only_shows_own_submissions(): void
    {
        // Student B creates their own submission
        $submissionB = WritingSubmission::create([
            'user_id' => $this->studentB->id,
            'writing_prompt_id' => $this->prompt->id,
            'essay_content' => 'Student B distinct private essay content here for testing separation.',
            'word_count' => 10,
            'time_spent_seconds' => 600,
            'status' => SubmissionStatus::SUBMITTED,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->studentA)
            ->get(route('student.writing.submissions.index'));

        $response->assertOk();
        $response->assertSee(route('student.writing.submissions.show', $this->submissionA));
        $response->assertDontSee(route('student.writing.submissions.show', $submissionB));
    }
}
