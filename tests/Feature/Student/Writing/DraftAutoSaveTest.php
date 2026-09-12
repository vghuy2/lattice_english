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

class DraftAutoSaveTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected WritingPrompt $prompt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create();

        $topic = VocabularyTopic::create([
            'title' => 'Urbanisation & Cities',
            'slug' => 'urbanisation-cities',
            'status' => ContentStatus::PUBLISHED,
            'order_index' => 1,
        ]);

        $this->prompt = WritingPrompt::create([
            'topic_id' => $topic->id,
            'title' => 'Essay: Traffic Congestion in Modern Cities',
            'slug' => 'traffic-congestion-modern-cities',
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::PROBLEM_SOLUTION,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'Traffic congestion is becoming a major issue in big cities. What are the causes and solutions?',
            'min_words' => 250,
            'time_limit_minutes' => 40,
            'order_index' => 1,
        ]);
    }

    public function test_student_can_auto_save_draft_via_ajax(): void
    {
        $payload = [
            'essay_content' => 'Traffic congestion is one of the most pressing challenges in contemporary urban areas.',
            'time_spent_seconds' => 180,
        ];

        $response = $this->actingAs($this->student)
            ->postJson(route('student.writing.draft', $this->prompt), $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'word_count' => 13,
        ]);

        $this->assertDatabaseHas('writing_submissions', [
            'user_id' => $this->student->id,
            'writing_prompt_id' => $this->prompt->id,
            'status' => SubmissionStatus::DRAFT->value,
            'word_count' => 13,
            'time_spent_seconds' => 180,
        ]);
    }

    public function test_subsequent_auto_saves_update_existing_draft(): void
    {
        // First save
        $this->actingAs($this->student)
            ->postJson(route('student.writing.draft', $this->prompt), [
                'essay_content' => 'First sentence.',
                'time_spent_seconds' => 60,
            ]);

        $this->assertEquals(1, WritingSubmission::where('user_id', $this->student->id)->count());

        // Second save with more content
        $this->actingAs($this->student)
            ->postJson(route('student.writing.draft', $this->prompt), [
                'essay_content' => 'First sentence. Second sentence added later.',
                'time_spent_seconds' => 120,
            ]);

        $this->assertEquals(1, WritingSubmission::where('user_id', $this->student->id)->count());

        $draft = WritingSubmission::where('user_id', $this->student->id)->first();
        $this->assertEquals(6, $draft->word_count);
        $this->assertEquals(120, $draft->time_spent_seconds);
        $this->assertEquals('First sentence. Second sentence added later.', $draft->essay_content);
    }
}
