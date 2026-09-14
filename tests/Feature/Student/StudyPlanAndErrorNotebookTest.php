<?php

namespace Tests\Feature\Student;

use App\Enums\ContentStatus;
use App\Enums\UserRole;
use App\Enums\VocabularyLevel;
use App\Enums\WordStudyStatus;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Models\StudentVocabularyReview;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyPlanAndErrorNotebookTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected VocabularyLesson $lesson;
    protected WritingPrompt $prompt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create([
            'role' => UserRole::STUDENT,
            'current_band' => 4.5,
            'target_band' => 6.5,
            'target_date' => now()->addMonths(3),
        ]);

        $topic = VocabularyTopic::create([
            'title' => 'Technology & Society',
            'slug' => 'technology-society',
            'status' => ContentStatus::PUBLISHED,
            'order_index' => 1,
        ]);

        $this->lesson = VocabularyLesson::create([
            'topic_id' => $topic->id,
            'title' => 'Artificial Intelligence & Automation',
            'slug' => 'ai-automation',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'order_index' => 1,
            'estimated_minutes' => 15,
        ]);

        $item = VocabularyItem::create([
            'lesson_id' => $this->lesson->id,
            'word' => 'Automation',
            'part_of_speech' => \App\Enums\PartOfSpeech::NOUN,
            'vietnamese_meaning' => 'Tự động hóa',
            'example_sentence' => 'Automation transforms modern industries.',
            'sort_order' => 1,
        ]);

        $this->prompt = WritingPrompt::create([
            'topic_id' => $topic->id,
            'title' => 'Impact of AI on Employment',
            'slug' => 'impact-of-ai-on-employment',
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::OPINION,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'Some people believe AI will replace jobs. To what extent do you agree?',
            'min_words' => 250,
            'time_limit_minutes' => 40,
            'order_index' => 1,
        ]);

        // Seed an error word
        StudentVocabularyReview::create([
            'user_id' => $this->student->id,
            'vocabulary_item_id' => $item->id,
            'status' => WordStudyStatus::REVIEW_NEEDED,
            'mastery_level' => 1,
            'incorrect_count' => 3,
            'correct_count' => 1,
            'review_count' => 4,
            'last_reviewed_at' => now(),
            'next_review_at' => now(),
        ]);
    }

    public function test_guest_cannot_access_study_plan(): void
    {
        $response = $this->get(route('student.study-plan'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_can_view_study_plan_with_milestones_and_recommendations(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.study-plan'));

        $response->assertOk();
        $response->assertSee('Lộ Trình Ôn Luyện Bứt Phá');
        $response->assertSee('Các Cột Mốc Chinh Phục (Milestones)');
        $response->assertSee('Bài học Từ vựng Tiếp theo');
        $response->assertSee('Đề Luyện Viết Khuyên Dùng');
        $response->assertSee($this->lesson->title);
        $response->assertSee($this->prompt->title);
    }

    public function test_guest_cannot_access_error_notebook(): void
    {
        $response = $this->get(route('student.error-notebook'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_can_view_error_notebook_with_flagged_words(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.error-notebook'));

        $response->assertOk();
        $response->assertSee('Sổ Tay Lỗi Sai Cá Nhân');
        $response->assertSee('Automation');
        $response->assertSee('Sai 3 lần');
        $response->assertSee('Tự động hóa');
    }
}
