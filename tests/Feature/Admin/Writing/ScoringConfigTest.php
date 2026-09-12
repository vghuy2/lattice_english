<?php

namespace Tests\Feature\Admin\Writing;

use App\Enums\ScoringCriterion;
use App\Enums\WritingTaskType;
use App\Models\ScoringRubric;
use App\Models\ScoringRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoringConfigTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected ScoringRubric $rubric;
    protected ScoringRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();

        $this->rubric = ScoringRubric::create([
            'task_type' => WritingTaskType::TASK_1,
            'criterion' => ScoringCriterion::TASK_ACHIEVEMENT_RESPONSE,
            'band_score' => 6.0,
            'description' => 'Mô tả ban đầu cho Band 6.0 Task 1 TA',
        ]);

        $this->rule = ScoringRule::create([
            'rule_key' => 'min_word_penalty',
            'name' => 'Quy tắc trừ điểm số từ tối thiểu',
            'task_type' => 'all',
            'criterion' => ScoringCriterion::TASK_ACHIEVEMENT_RESPONSE,
            'parameters' => ['min_words' => 150],
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_scoring_dashboard(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.writing.scoring.index'));

        $response->assertOk();
        $response->assertSee('Rubrics');
        $response->assertSee('Quy tắc trừ điểm số từ tối thiểu');
    }

    public function test_admin_can_update_rubric_description(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.writing.scoring.rubrics.update', $this->rubric), [
                'description' => 'Mô tả đã được cập nhật chuẩn hóa theo Cambridge IELTS 19.',
            ]);

        $this->rubric->refresh();
        $this->assertEquals('Mô tả đã được cập nhật chuẩn hóa theo Cambridge IELTS 19.', $this->rubric->description);

        $response->assertRedirect();
    }

    public function test_admin_can_toggle_scoring_rule(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.writing.scoring.rules.toggle', $this->rule));

        $this->rule->refresh();
        $this->assertFalse($this->rule->is_active);

        // Toggle back
        $this->actingAs($this->admin)
            ->post(route('admin.writing.scoring.rules.toggle', $this->rule));

        $this->rule->refresh();
        $this->assertTrue($this->rule->is_active);
    }

    public function test_admin_can_update_scoring_rule_parameters(): void
    {
        $newParams = json_encode(['min_words' => 160, 'penalty' => -1.0]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.writing.scoring.rules.update', $this->rule), [
                'name' => 'Quy tắc kiểm tra số từ tối thiểu (Nâng cao)',
                'description' => 'Quy tắc nghiêm ngặt hơn.',
                'parameters' => $newParams,
            ]);

        $this->rule->refresh();
        $this->assertEquals('Quy tắc kiểm tra số từ tối thiểu (Nâng cao)', $this->rule->name);
        $this->assertEquals(160, $this->rule->parameters['min_words']);

        $response->assertRedirect();
    }
}
