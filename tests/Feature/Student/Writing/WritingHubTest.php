<?php

namespace Tests\Feature\Student\Writing;

use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Models\User;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WritingHubTest extends TestCase
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
            'title' => 'Education & Society',
            'slug' => 'education-society',
            'status' => ContentStatus::PUBLISHED,
            'order_index' => 1,
        ]);

        $this->task1Prompt = WritingPrompt::create([
            'topic_id' => $this->topic->id,
            'title' => 'Bar Chart: Internet Usage in 2020',
            'slug' => 'bar-chart-internet-usage-2020',
            'task_type' => WritingTaskType::TASK_1,
            'prompt_type' => WritingPromptType::BAR_CHART,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'The bar chart below shows the percentage of people using the internet in different age groups.',
            'min_words' => 150,
            'time_limit_minutes' => 20,
            'suggested_outline' => [
                ['section' => 'Introduction', 'hint' => 'Paraphrase the given prompt.'],
                ['section' => 'Overview', 'hint' => 'Highlight main trends.'],
            ],
            'order_index' => 1,
        ]);

        $this->task2Prompt = WritingPrompt::create([
            'topic_id' => $this->topic->id,
            'title' => 'Essay: Online Education vs Traditional Classrooms',
            'slug' => 'essay-online-education-vs-traditional',
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::OPINION,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'Some people believe online education is more effective than traditional learning. To what extent do you agree?',
            'min_words' => 250,
            'time_limit_minutes' => 40,
            'order_index' => 2,
        ]);

        $this->draftPrompt = WritingPrompt::create([
            'topic_id' => $this->topic->id,
            'title' => 'Draft Unpublished Prompt',
            'slug' => 'draft-unpublished-prompt',
            'task_type' => WritingTaskType::TASK_1,
            'prompt_type' => WritingPromptType::LINE_GRAPH,
            'level' => VocabularyLevel::BAND_5_5_6_0,
            'status' => ContentStatus::DRAFT,
            'prompt_text' => 'This is a draft prompt not ready for students.',
            'min_words' => 150,
            'time_limit_minutes' => 20,
            'order_index' => 3,
        ]);
    }

    public function test_student_can_view_writing_hub_with_published_prompts(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.writing.index'));

        $response->assertOk();
        $response->assertSee('Phòng Luyện viết IELTS Writing');
        $response->assertSee('Bar Chart: Internet Usage in 2020');
        $response->assertSee('Essay: Online Education vs Traditional Classrooms');
        $response->assertDontSee('Draft Unpublished Prompt');
    }

    public function test_student_can_filter_writing_prompts_by_task_type(): void
    {
        // Filter Task 1
        $responseTask1 = $this->actingAs($this->student)
            ->get(route('student.writing.index', ['task_type' => 'task_1']));

        $responseTask1->assertOk();
        $responseTask1->assertSee('Bar Chart: Internet Usage in 2020');
        $responseTask1->assertDontSee('Essay: Online Education vs Traditional Classrooms');

        // Filter Task 2
        $responseTask2 = $this->actingAs($this->student)
            ->get(route('student.writing.index', ['task_type' => 'task_2']));

        $responseTask2->assertOk();
        $responseTask2->assertSee('Essay: Online Education vs Traditional Classrooms');
        $responseTask2->assertDontSee('Bar Chart: Internet Usage in 2020');
    }

    public function test_student_can_search_prompts(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.writing.index', ['search' => 'Traditional Classrooms']));

        $response->assertOk();
        $response->assertSee('Essay: Online Education vs Traditional Classrooms');
        $response->assertDontSee('Bar Chart: Internet Usage in 2020');
    }

    public function test_student_can_view_published_prompt_details(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.writing.show', $this->task1Prompt));

        $response->assertOk();
        $response->assertSee('Bar Chart: Internet Usage in 2020');
        $response->assertSee('The bar chart below shows the percentage of people using the internet');
        $response->assertSee('Vào phòng Luyện viết');
    }

    public function test_student_cannot_view_draft_prompt(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.writing.show', $this->draftPrompt));

        $response->assertNotFound();
    }
}
