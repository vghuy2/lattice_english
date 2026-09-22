<?php

namespace Tests\Feature\Scoring;

use App\Models\ScoringRule;
use App\Models\User;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use App\Services\Scoring\WritingScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ScoringRulesCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_scoring_rules_are_cached_on_evaluation(): void
    {
        Cache::forget('active_scoring_rules');
        $this->assertFalse(Cache::has('active_scoring_rules'));

        $scoringService = app(WritingScoringService::class);

        $prompt = WritingPrompt::create([
            'title' => 'Test Prompt',
            'slug' => 'test-prompt',
            'prompt_text' => 'The chart below shows export figures from 2000 to 2020.',
            'task_type' => \App\Enums\WritingTaskType::TASK_1,
            'prompt_type' => \App\Enums\WritingPromptType::BAR_CHART,
            'min_words' => 150,
            'status' => \App\Enums\ContentStatus::PUBLISHED,
        ]);

        $analysis = app(\App\Services\Scoring\TextAnalysisService::class)->analyze(
            'Overall, the chart illustrates a significant increase in production. In addition, the numbers continued to rise steadily throughout the period.'
        );

        $scoringService->evaluate($analysis, $prompt);

        $this->assertTrue(Cache::has('active_scoring_rules'));
    }

    public function test_cache_is_invalidated_when_admin_updates_or_toggles_rule(): void
    {
        Cache::put('active_scoring_rules', collect(['dummy' => true]), 3600);
        $this->assertTrue(Cache::has('active_scoring_rules'));

        $admin = User::factory()->create(['role' => 'admin']);

        $rule = ScoringRule::create([
            'rule_key' => 'test_rule',
            'name' => 'Test Rule',
            'task_type' => 'task_1',
            'criterion' => 'task_achievement_response',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.writing.scoring.rules.toggle', $rule));
        $response->assertRedirect();

        $this->assertFalse(Cache::has('active_scoring_rules'), 'Cache should be invalidated after toggling rule.');
    }
}
