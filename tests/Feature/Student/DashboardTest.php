<?php

namespace Tests\Feature\Student;

use App\Enums\ContentStatus;
use App\Enums\LearningStatus;
use App\Enums\SubmissionStatus;
use App\Enums\VocabularyLevel;
use App\Enums\WordStudyStatus;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Models\StudentLessonProgress;
use App\Models\StudentVocabularyReview;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected VocabularyTopic $topic;
    protected VocabularyLesson $lesson;
    protected WritingPrompt $prompt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create([
            'name' => 'Nguyen Van A',
            'current_band' => 4.5,
            'target_band' => 6.5,
        ]);

        $this->topic = VocabularyTopic::create([
            'title' => 'Environment & Nature',
            'slug' => 'environment-nature',
            'status' => ContentStatus::PUBLISHED,
            'order_index' => 1,
        ]);

        $this->lesson = VocabularyLesson::create([
            'topic_id' => $this->topic->id,
            'title' => 'Global Warming & Climate Change',
            'slug' => 'global-warming-climate-change',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'order_index' => 1,
            'estimated_minutes' => 15,
        ]);

        $item = VocabularyItem::create([
            'lesson_id' => $this->lesson->id,
            'word' => 'Deforestation',
            'part_of_speech' => \App\Enums\PartOfSpeech::NOUN,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'vietnamese_meaning' => 'Nạn phá rừng',
            'example_sentence' => 'Deforestation is one of the main drivers of climate change.',
            'order_index' => 1,
        ]);

        $this->prompt = WritingPrompt::create([
            'topic_id' => $this->topic->id,
            'title' => 'Essay: Climate Change Solutions',
            'slug' => 'essay-climate-change-solutions',
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::PROBLEM_SOLUTION,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'What are the main causes of climate change and how can governments address it?',
            'min_words' => 250,
            'time_limit_minutes' => 40,
            'order_index' => 1,
        ]);
    }

    public function test_student_can_view_dashboard_with_profile_and_stats(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSee('Xin chào, Nguyen Van A!');
        $response->assertSee('Band 4.5');
        $response->assertSee('Band 6.5');
        $response->assertSee('Thư viện Từ vựng');
        $response->assertSee('Phòng Luyện Writing');
    }

    public function test_dashboard_displays_due_review_alert_when_words_need_review(): void
    {
        $item = VocabularyItem::first();

        // Create a review due yesterday
        StudentVocabularyReview::create([
            'user_id' => $this->student->id,
            'vocabulary_item_id' => $item->id,
            'status' => WordStudyStatus::LEARNING,
            'interval_days' => 1,
            'next_review_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSee('cần ôn tập hôm nay');
        $response->assertSee('Bắt đầu ôn tập ngay');
    }

    public function test_dashboard_displays_writing_metrics_and_recent_submissions(): void
    {
        WritingSubmission::create([
            'user_id' => $this->student->id,
            'writing_prompt_id' => $this->prompt->id,
            'essay_content' => 'Sample submitted essay content here for testing...',
            'word_count' => 260,
            'time_spent_seconds' => 1900,
            'status' => SubmissionStatus::GRADED,
            'overall_score' => 6.0,
            'ta_score' => 6.0,
            'cc_score' => 6.0,
            'lr_score' => 6.0,
            'gra_score' => 6.0,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSee('Band 6.0');
        $response->assertSee('Essay: Climate Change Solutions');
    }

    public function test_dashboard_shows_in_progress_learning_card(): void
    {
        StudentLessonProgress::create([
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'status' => LearningStatus::IN_PROGRESS,
            'completed_items_count' => 1,
            'total_items_count' => 5,
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSee('Tiếp tục lộ trình');
        $response->assertSee('Global Warming & Climate Change');
        $response->assertSee('Học tiếp →');
    }
}
