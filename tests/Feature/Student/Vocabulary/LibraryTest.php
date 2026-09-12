<?php

namespace Tests\Feature\Student\Vocabulary;

use App\Enums\ContentStatus;
use App\Enums\UserRole;
use App\Enums\VocabularyLevel;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected VocabularyTopic $topic;
    protected VocabularyLesson $publishedLesson;
    protected VocabularyLesson $draftLesson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create();

        $this->topic = VocabularyTopic::create([
            'title' => 'Education & Academic Life',
            'slug' => 'education-academic-life',
            'status' => ContentStatus::PUBLISHED,
            'order_index' => 1,
        ]);

        $this->publishedLesson = VocabularyLesson::create([
            'topic_id' => $this->topic->id,
            'title' => 'University Study & Degrees',
            'slug' => 'university-study-degrees',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'order_index' => 1,
            'estimated_minutes' => 15,
        ]);

        VocabularyItem::create([
            'lesson_id' => $this->publishedLesson->id,
            'word' => 'Curriculum',
            'part_of_speech' => \App\Enums\PartOfSpeech::NOUN,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'vietnamese_meaning' => 'Chương trình giảng dạy',
            'example_sentence' => 'The school curriculum includes computer science.',
            'order_index' => 1,
        ]);

        $this->draftLesson = VocabularyLesson::create([
            'topic_id' => $this->topic->id,
            'title' => 'Draft Unpublished Lesson',
            'slug' => 'draft-unpublished-lesson',
            'level' => VocabularyLevel::BAND_5_5_6_0,
            'status' => ContentStatus::DRAFT,
            'order_index' => 2,
            'estimated_minutes' => 10,
        ]);
    }

    public function test_student_can_view_vocabulary_library(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.vocabulary.index'));

        $response->assertOk();
        $response->assertSee('Thư viện Từ vựng IELTS');
        $response->assertSee('University Study & Degrees');
        $response->assertDontSee('Draft Unpublished Lesson');
    }

    public function test_student_can_filter_library_by_topic_and_level(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.vocabulary.index', [
                'topic_id' => $this->topic->id,
                'level' => VocabularyLevel::BAND_4_5_5_0->value,
            ]));

        $response->assertOk();
        $response->assertSee('University Study & Degrees');
    }

    public function test_student_can_view_published_lesson_in_cards_and_list_mode(): void
    {
        // Cards mode
        $resCards = $this->actingAs($this->student)
            ->get(route('student.vocabulary.lessons.show', [$this->publishedLesson, 'mode' => 'cards']));

        $resCards->assertOk();
        $resCards->assertSee('Curriculum');
        $resCards->assertSee('Chương trình giảng dạy');

        // List mode
        $resList = $this->actingAs($this->student)
            ->get(route('student.vocabulary.lessons.show', [$this->publishedLesson, 'mode' => 'list']));

        $resList->assertOk();
        $resList->assertSee('Curriculum');
    }

    public function test_student_cannot_view_draft_or_unpublished_lesson(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.vocabulary.lessons.show', $this->draftLesson));

        $response->assertNotFound();
    }
}
