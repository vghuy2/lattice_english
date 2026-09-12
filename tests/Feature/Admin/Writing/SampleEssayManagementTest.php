<?php

namespace Tests\Feature\Admin\Writing;

use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Models\SampleEssay;
use App\Models\User;
use App\Models\WritingPrompt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SampleEssayManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected WritingPrompt $prompt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();

        $this->prompt = WritingPrompt::create([
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::OPINION,
            'title' => 'Remote Working Effects',
            'slug' => 'remote-working-effects',
            'prompt_text' => 'More and more people work remotely from home. Is this a positive or negative development?',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'min_words' => 250,
            'time_limit_minutes' => 40,
            'status' => ContentStatus::PUBLISHED,
        ]);
    }

    public function test_admin_can_add_sample_essay_to_prompt(): void
    {
        $essayText = "In recent years, the prevalence of telecommuting has expanded considerably across the globe. While this trend introduces certain interpersonal challenges, I firmly believe that its advantages regarding workplace flexibility and environmental benefits far outweigh any drawbacks.

To begin with, working from home substantially reduces daily commuting time and operational expenses. Employees can achieve a superior work-life balance and focus on productivity without navigating congested urban traffic. Furthermore, telecommuting diminishes carbon emissions associated with daily vehicular transit.

On the other hand, remote employment may lead to professional isolation and difficulties in team cohesion. Nevertheless, modern digital communication platforms have successfully mitigated these shortcomings through virtual conferencing.

In conclusion, remote working represents a highly progressive shift that enhances employee well-being and ecological sustainability.";

        $postData = [
            'title' => 'Bài mẫu Band 7.0 (Mẫu mực & Từ vựng Telecommuting)',
            'band_score' => 7.0,
            'author_type' => 'Giảng viên Lattice',
            'essay_text' => $essayText,
            'analysis_notes' => 'Bài viết có lập trường rõ ràng, từ vựng C1 phong phú và phân đoạn logic.',
            'highlighted_vocabulary' => [
                ['word' => 'telecommuting', 'meaning' => 'làm việc từ xa', 'band' => '7.5', 'note' => 'Paraphrase hoàn hảo cho remote working'],
                ['word' => 'superior work-life balance', 'meaning' => 'sự cân bằng công việc - cuộc sống vượt trội', 'band' => '7.0', 'note' => 'Collocation học thuật'],
            ],
            'highlighted_structures' => [
                ['pattern' => 'While this trend introduces..., I firmly believe that...', 'note' => 'Mở bài nhượng bộ kinh điển'],
            ],
            'status' => 'published',
            'order_index' => 1,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.writing.prompts.samples.store', $this->prompt), $postData);

        $essay = SampleEssay::where('writing_prompt_id', $this->prompt->id)->first();
        $this->assertNotNull($essay);
        $this->assertEquals(7.0, $essay->band_score);
        $this->assertGreaterThan(100, $essay->word_count);
        $this->assertEquals(2, count($essay->highlighted_vocabulary));

        $response->assertRedirect(route('admin.writing.prompts.show', $this->prompt));
    }

    public function test_admin_can_update_sample_essay(): void
    {
        $essay = SampleEssay::create([
            'writing_prompt_id' => $this->prompt->id,
            'title' => 'Bài mẫu sơ lược',
            'band_score' => 5.0,
            'author_type' => 'Tự soạn',
            'essay_text' => 'This is a short sample essay text for remote working in modern life.',
            'word_count' => 13,
            'status' => 'published',
        ]);

        $updateData = [
            'title' => 'Bài mẫu Band 5.5 (Đã bổ sung phân tích)',
            'band_score' => 5.5,
            'author_type' => 'Giảng viên sửa đổi',
            'essay_text' => 'This is an expanded sample essay text containing more than fifty words to properly meet the validation rules of the application test suite for IELTS writing tasks.',
            'analysis_notes' => 'Cần nâng cấp thêm collocations.',
            'status' => 'published',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.writing.prompts.samples.update', [$this->prompt, $essay]), $updateData);

        $essay->refresh();
        $this->assertEquals(5.5, $essay->band_score);
        $this->assertEquals('Bài mẫu Band 5.5 (Đã bổ sung phân tích)', $essay->title);

        $response->assertRedirect(route('admin.writing.prompts.show', $this->prompt));
    }

    public function test_admin_can_delete_sample_essay(): void
    {
        $essay = SampleEssay::create([
            'writing_prompt_id' => $this->prompt->id,
            'title' => 'Bài mẫu xóa',
            'band_score' => 5.0,
            'author_type' => 'Tự soạn',
            'essay_text' => 'Sample essay text for deletion testing purposes in the admin area.',
            'word_count' => 10,
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.writing.prompts.samples.destroy', [$this->prompt, $essay]));

        $this->assertDatabaseMissing('sample_essays', [
            'id' => $essay->id,
        ]);

        $response->assertRedirect(route('admin.writing.prompts.show', $this->prompt));
    }
}
