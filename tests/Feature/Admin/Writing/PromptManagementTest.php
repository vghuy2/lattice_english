<?php

namespace Tests\Feature\Admin\Writing;

use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Models\User;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromptManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected VocabularyTopic $topic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();

        $this->topic = VocabularyTopic::create([
            'title' => 'Education',
            'slug' => 'education',
            'status' => ContentStatus::PUBLISHED,
        ]);
    }

    public function test_admin_can_view_prompts_list(): void
    {
        WritingPrompt::create([
            'task_type' => WritingTaskType::TASK_1,
            'prompt_type' => WritingPromptType::LINE_GRAPH,
            'title' => 'Car Ownership Trends',
            'slug' => 'car-ownership-trends',
            'prompt_text' => 'The line graph shows car ownership in the UK...',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'min_words' => 150,
            'time_limit_minutes' => 20,
            'status' => ContentStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.writing.prompts.index'));

        $response->assertOk();
        $response->assertSee('Quản lý Đề bài IELTS Writing');
        $response->assertSee('Car Ownership Trends');
    }

    public function test_admin_can_filter_prompts_by_task_type_and_level(): void
    {
        WritingPrompt::create([
            'task_type' => WritingTaskType::TASK_1,
            'prompt_type' => WritingPromptType::LINE_GRAPH,
            'title' => 'Car Ownership Trends',
            'slug' => 'car-ownership-trends',
            'prompt_text' => 'The line graph shows car ownership...',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
        ]);

        WritingPrompt::create([
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::OPINION,
            'title' => 'Artificial Intelligence in Education',
            'slug' => 'ai-in-education',
            'prompt_text' => 'Some people believe AI should replace teachers...',
            'level' => VocabularyLevel::BAND_5_5_6_0,
            'status' => ContentStatus::PUBLISHED,
        ]);

        $resTask1 = $this->actingAs($this->admin)
            ->get(route('admin.writing.prompts.index', ['task_type' => 'task_1']));

        $resTask1->assertOk();
        $resTask1->assertSee('Car Ownership Trends');
        $resTask1->assertDontSee('Artificial Intelligence in Education');
    }

    public function test_admin_can_create_new_writing_prompt(): void
    {
        $postData = [
            'task_type' => WritingTaskType::TASK_2->value,
            'prompt_type' => WritingPromptType::DISCUSSION->value,
            'topic_id' => $this->topic->id,
            'title' => 'University Tuition Fees Debate',
            'slug' => 'university-tuition-fees-debate',
            'prompt_text' => 'Some people think tertiary education should be free for all students. Discuss both views.',
            'level' => VocabularyLevel::BAND_4_5_5_0->value,
            'min_words' => 250,
            'time_limit_minutes' => 40,
            'guidance' => 'Nêu rõ 2 quan điểm và đưa ra lập trường cân bằng.',
            'outline_sections' => [
                ['section' => 'Introduction', 'hint' => 'Paraphrase đề bài'],
                ['section' => 'Body 1', 'hint' => 'Lợi ích của miễn học phí'],
            ],
            'status' => ContentStatus::PUBLISHED->value,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.writing.prompts.store'), $postData);

        $prompt = WritingPrompt::where('slug', 'university-tuition-fees-debate')->first();
        $this->assertNotNull($prompt);
        $this->assertEquals(WritingTaskType::TASK_2, $prompt->task_type);
        $this->assertEquals(2, count($prompt->suggested_outline));

        $response->assertRedirect(route('admin.writing.prompts.show', $prompt));
    }

    public function test_admin_can_update_writing_prompt(): void
    {
        $prompt = WritingPrompt::create([
            'task_type' => WritingTaskType::TASK_1,
            'prompt_type' => WritingPromptType::BAR_CHART,
            'title' => 'Energy Production Bar Chart',
            'slug' => 'energy-production-bar-chart',
            'prompt_text' => 'The bar chart shows energy production...',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'min_words' => 150,
            'time_limit_minutes' => 20,
            'status' => ContentStatus::DRAFT,
        ]);

        $updateData = [
            'task_type' => WritingTaskType::TASK_1->value,
            'prompt_type' => WritingPromptType::BAR_CHART->value,
            'title' => 'Renewable Energy Production Bar Chart (Updated)',
            'slug' => 'energy-production-bar-chart-updated',
            'prompt_text' => 'The bar chart shows renewable energy production...',
            'level' => VocabularyLevel::BAND_5_5_6_0->value,
            'min_words' => 150,
            'time_limit_minutes' => 20,
            'status' => ContentStatus::PUBLISHED->value,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.writing.prompts.update', $prompt), $updateData);

        $prompt->refresh();
        $this->assertEquals('Renewable Energy Production Bar Chart (Updated)', $prompt->title);
        $this->assertEquals(ContentStatus::PUBLISHED, $prompt->status);

        $response->assertRedirect(route('admin.writing.prompts.show', $prompt));
    }

    public function test_admin_can_delete_writing_prompt(): void
    {
        $prompt = WritingPrompt::create([
            'task_type' => WritingTaskType::TASK_1,
            'prompt_type' => WritingPromptType::MAP,
            'title' => 'City Center Map Evolution',
            'slug' => 'city-center-map-evolution',
            'prompt_text' => 'The maps show changes to a city center...',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'min_words' => 150,
            'time_limit_minutes' => 20,
            'status' => ContentStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.writing.prompts.destroy', $prompt));

        $this->assertDatabaseMissing('writing_prompts', [
            'id' => $prompt->id,
        ]);

        $response->assertRedirect(route('admin.writing.prompts.index'));
    }

    public function test_admin_can_toggle_prompt_status(): void
    {
        $prompt = WritingPrompt::create([
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::OPINION,
            'title' => 'Space Exploration Investment',
            'slug' => 'space-exploration-investment',
            'prompt_text' => 'Governments spend too much money on space exploration...',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::DRAFT,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.writing.prompts.toggle-status', $prompt));

        $prompt->refresh();
        $this->assertEquals(ContentStatus::PUBLISHED, $prompt->status);

        $response->assertRedirect();
    }

    public function test_admin_can_create_prompt_with_diagram_image(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('bar_chart.png', 600, 400);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.writing.prompts.store'), [
                'task_type' => WritingTaskType::TASK_1->value,
                'prompt_type' => WritingPromptType::BAR_CHART->value,
                'topic_id' => $this->topic->id,
                'title' => 'Energy Production Bar Chart',
                'prompt_text' => 'The bar chart below shows energy production from various sources in 2020.',
                'image' => $file,
                'level' => VocabularyLevel::BAND_4_5_5_0->value,
                'min_words' => 150,
                'time_limit_minutes' => 20,
                'status' => ContentStatus::PUBLISHED->value,
            ]);

        $prompt = WritingPrompt::where('title', 'Energy Production Bar Chart')->first();
        $this->assertNotNull($prompt);
        $this->assertNotNull($prompt->image_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($prompt->image_path);

        $response->assertRedirect(route('admin.writing.prompts.show', $prompt));
    }

    public function test_admin_can_parse_prompt_from_ocr_text(): void
    {
        $rawOcrText = "WRITING TASK 1\nYou should spend about 20 minutes on this task.\nThe bar chart below shows the percentage of households with internet access in three European countries between 2010 and 2020.\nSummarise the information by selecting and reporting the main features, and make comparisons where relevant.\nWrite at least 150 words.";

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.writing.prompts.ocr-image'), [
                'raw_text' => $rawOcrText,
            ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.task_type', 'task_1');
        $response->assertJsonPath('data.prompt_type', 'bar_chart');
        $response->assertJsonPath('data.min_words', 150);
        $response->assertJsonPath('data.time_limit_minutes', 20);
        $this->assertNotEmpty($response->json('data.title'));
        $this->assertStringContainsString('households with internet access', $response->json('data.prompt_text'));
    }
}
