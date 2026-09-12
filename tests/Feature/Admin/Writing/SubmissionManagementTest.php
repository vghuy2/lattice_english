<?php

namespace Tests\Feature\Admin\Writing;

use App\Enums\ContentStatus;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Models\User;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use Database\Seeders\WritingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected WritingPrompt $prompt;
    protected WritingSubmission $submission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WritingSeeder::class);

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $this->student = User::factory()->create([
            'role' => UserRole::STUDENT,
            'name' => 'Nguyen Van B',
            'email' => 'student_b@ielts.vn',
        ]);

        $topic = VocabularyTopic::create([
            'title' => 'Economy & Business',
            'slug' => 'economy-business',
            'status' => ContentStatus::PUBLISHED,
            'order_index' => 1,
        ]);

        $this->prompt = WritingPrompt::create([
            'topic_id' => $topic->id,
            'title' => 'Bar Chart: Economic Growth in Asia',
            'slug' => 'bar-chart-economic-growth-asia',
            'task_type' => WritingTaskType::TASK_1,
            'prompt_type' => WritingPromptType::BAR_CHART,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'The bar chart shows economic growth rates in Asian countries between 2015 and 2020.',
            'min_words' => 150,
            'time_limit_minutes' => 20,
            'order_index' => 1,
        ]);

        $essay = "The bar chart illustrates economic growth rates across several Asian nations from 2015 to 2020.\n\nOverall, it is clear that all countries experienced positive growth throughout the period, although rates fluctuated. In addition, country X recorded the most rapid expansion.\n\nIn 2015, the growth rate for country X stood at 5%, but it rose steadily to reach 8% in 2020. However, country Y began at 4% and remained relatively stable around that level.\n\nIn conclusion, the economic expansion was noticeable across the entire region during the five years.";

        $this->submission = WritingSubmission::create([
            'user_id' => $this->student->id,
            'writing_prompt_id' => $this->prompt->id,
            'essay_content' => $essay,
            'word_count' => 95,
            'time_spent_seconds' => 1200,
            'status' => SubmissionStatus::SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function test_admin_can_view_submissions_index_with_search_and_filters(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.writing.submissions.index'));

        $response->assertOk();
        $response->assertSee('Quản lý Bài nộp IELTS Writing');
        $response->assertSee('Nguyen Van B');
        $response->assertSee('Bar Chart: Economic Growth in Asia');
    }

    public function test_admin_can_filter_submissions_by_search_term(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.writing.submissions.index', ['search' => 'student_b@ielts.vn']));

        $response->assertOk();
        $response->assertSee('Nguyen Van B');
    }

    public function test_admin_can_view_submission_detail_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.writing.submissions.show', $this->submission));

        $response->assertOk();
        $response->assertSee('Bài nộp của: Nguyen Van B');
        $response->assertSee('Chấm lại tự động');
        $response->assertSee('Điều chỉnh điểm');
    }

    public function test_admin_can_trigger_rescore_on_submission(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.writing.submissions.rescore', $this->submission));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->submission->refresh();
        $this->assertEquals(SubmissionStatus::GRADED, $this->submission->status);
        $this->assertNotNull($this->submission->overall_score);
        $this->assertNotNull($this->submission->ta_score);
        $this->assertNotNull($this->submission->feedback_notes);
    }

    public function test_admin_can_manually_update_feedback_and_override_scores(): void
    {
        $payload = [
            'overall_score' => 6.5,
            'ta_score' => 6.5,
            'cc_score' => 6.5,
            'lr_score' => 6.0,
            'gra_score' => 7.0,
            'feedback_notes' => 'Teacher manual commentary: Great essay structure and good lexical range!',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.writing.submissions.feedback', $this->submission), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->submission->refresh();
        $this->assertEquals(6.5, $this->submission->overall_score);
        $this->assertEquals(7.0, $this->submission->gra_score);
        $this->assertStringContainsString('Teacher manual commentary', $this->submission->feedback_notes);
    }

    public function test_student_cannot_access_admin_submissions_routes(): void
    {
        $this->actingAs($this->student)
            ->get(route('admin.writing.submissions.index'))
            ->assertForbidden();

        $this->actingAs($this->student)
            ->get(route('admin.writing.submissions.show', $this->submission))
            ->assertForbidden();

        $this->actingAs($this->student)
            ->post(route('admin.writing.submissions.rescore', $this->submission))
            ->assertForbidden();
    }
}
