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

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected WritingPrompt $prompt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create();

        $topic = VocabularyTopic::create([
            'title' => 'Health & Lifestyle',
            'slug' => 'health-lifestyle',
            'status' => ContentStatus::PUBLISHED,
            'order_index' => 1,
        ]);

        $this->prompt = WritingPrompt::create([
            'topic_id' => $topic->id,
            'title' => 'Essay: Fast Food and Public Health',
            'slug' => 'fast-food-public-health',
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::OPINION,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'Fast food is considered harmful to public health. Do you agree or disagree?',
            'min_words' => 250,
            'time_limit_minutes' => 40,
            'order_index' => 1,
        ]);
    }

    public function test_student_can_successfully_submit_an_essay(): void
    {
        $essay = 'In recent years, the consumption of fast food has increased dramatically across the globe. This trend has raised serious concerns regarding public health and obesity rates. In my opinion, I strongly agree that fast food poses significant threats to human well-being, and governments should take proactive measures to regulate fast-food advertisements and introduce health education programs in schools.';

        $response = $this->actingAs($this->student)
            ->post(route('student.writing.submit', $this->prompt), [
                'essay_content' => $essay,
                'time_spent_seconds' => 1800,
            ]);

        $submission = WritingSubmission::where('user_id', $this->student->id)
            ->where('writing_prompt_id', $this->prompt->id)
            ->first();

        $this->assertNotNull($submission);
        $this->assertEquals(SubmissionStatus::GRADED, $submission->status);
        $this->assertEquals(58, $submission->word_count);
        $this->assertEquals(1800, $submission->time_spent_seconds);
        $this->assertNotNull($submission->submitted_at);

        $response->assertRedirect(route('student.writing.submissions.show', $submission));
        $response->assertSessionHas('success');
    }

    public function test_existing_draft_is_converted_to_submitted_upon_submission(): void
    {
        // Existing draft
        $draft = WritingSubmission::create([
            'user_id' => $this->student->id,
            'writing_prompt_id' => $this->prompt->id,
            'essay_content' => 'Old draft text that was incomplete...',
            'word_count' => 6,
            'time_spent_seconds' => 400,
            'status' => SubmissionStatus::DRAFT,
        ]);

        $finalEssay = 'This is the complete essay that contains sufficient characters and words to pass the minimum validation requirements for IELTS Writing task submission.';

        $response = $this->actingAs($this->student)
            ->post(route('student.writing.submit', $this->prompt), [
                'essay_content' => $finalEssay,
                'time_spent_seconds' => 1200,
            ]);

        $this->assertEquals(1, WritingSubmission::where('user_id', $this->student->id)->count());

        $draft->refresh();
        $this->assertEquals(SubmissionStatus::GRADED, $draft->status);
        $this->assertEquals($finalEssay, $draft->essay_content);
        $this->assertEquals(1200, $draft->time_spent_seconds);
        $response->assertRedirect(route('student.writing.submissions.show', $draft));
    }

    public function test_submitting_too_short_essay_fails_validation(): void
    {
        $response = $this->actingAs($this->student)
            ->post(route('student.writing.submit', $this->prompt), [
                'essay_content' => 'Too short',
                'time_spent_seconds' => 30,
            ]);

        $response->assertSessionHasErrors(['essay_content']);
        $this->assertDatabaseMissing('writing_submissions', [
            'user_id' => $this->student->id,
            'status' => SubmissionStatus::SUBMITTED->value,
        ]);
    }
}
