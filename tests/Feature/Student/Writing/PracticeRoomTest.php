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

class PracticeRoomTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected VocabularyTopic $topic;
    protected WritingPrompt $task1Prompt;
    protected WritingPrompt $task2Prompt;
    protected WritingPrompt $draftPrompt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create();

        $this->topic = VocabularyTopic::create([
            'title' => 'Environment & Technology',
            'slug' => 'environment-technology',
            'status' => ContentStatus::PUBLISHED,
            'order_index' => 1,
        ]);

        $this->task1Prompt = WritingPrompt::create([
            'topic_id' => $this->topic->id,
            'title' => 'Line Graph: Global Temperature Changes',
            'slug' => 'line-graph-global-temperature',
            'task_type' => WritingTaskType::TASK_1,
            'prompt_type' => WritingPromptType::LINE_GRAPH,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'The line graph illustrates global temperature anomalies between 1900 and 2000.',
            'min_words' => 150,
            'time_limit_minutes' => 20,
            'order_index' => 1,
        ]);

        $this->task2Prompt = WritingPrompt::create([
            'topic_id' => $this->topic->id,
            'title' => 'Essay: Renewable Energy Transition',
            'slug' => 'essay-renewable-energy-transition',
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::ADVANTAGES_DISADVANTAGES,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'Discuss advantages and disadvantages of shifting completely to solar energy.',
            'min_words' => 250,
            'time_limit_minutes' => 40,
            'order_index' => 2,
        ]);

        $this->draftPrompt = WritingPrompt::create([
            'topic_id' => $this->topic->id,
            'title' => 'Draft Prompt',
            'slug' => 'draft-prompt',
            'task_type' => WritingTaskType::TASK_1,
            'prompt_type' => WritingPromptType::BAR_CHART,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::DRAFT,
            'prompt_text' => 'Draft instruction.',
            'min_words' => 150,
            'time_limit_minutes' => 20,
            'order_index' => 3,
        ]);
    }

    public function test_student_can_enter_exam_room_for_published_task1(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.writing.practice', $this->task1Prompt));

        $response->assertOk();
        $response->assertSee('Line Graph: Global Temperature Changes');
        $response->assertSee('150'); // Task 1 word target
    }

    public function test_student_can_enter_exam_room_for_published_task2(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.writing.practice', $this->task2Prompt));

        $response->assertOk();
        $response->assertSee('Essay: Renewable Energy Transition');
        $response->assertSee('250'); // Task 2 word target
    }

    public function test_practice_room_loads_existing_draft_if_available(): void
    {
        WritingSubmission::create([
            'user_id' => $this->student->id,
            'writing_prompt_id' => $this->task2Prompt->id,
            'essay_content' => 'Nowadays, renewable energy plays a crucial role in protecting our planet.',
            'word_count' => 11,
            'time_spent_seconds' => 320,
            'status' => SubmissionStatus::DRAFT,
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.writing.practice', $this->task2Prompt));

        $response->assertOk();
        $response->assertSee('Nowadays, renewable energy plays a crucial role');
    }

    public function test_student_cannot_enter_exam_room_for_draft_prompt(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.writing.practice', $this->draftPrompt));

        $response->assertNotFound();
    }
}
