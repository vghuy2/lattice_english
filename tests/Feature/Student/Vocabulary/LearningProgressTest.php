<?php

namespace Tests\Feature\Student\Vocabulary;

use App\Enums\ContentStatus;
use App\Enums\LearningStatus;
use App\Enums\PartOfSpeech;
use App\Enums\UserRole;
use App\Enums\VocabularyLevel;
use App\Enums\WordStudyStatus;
use App\Models\StudentLessonProgress;
use App\Models\StudentVocabularyReview;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningProgressTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected VocabularyLesson $lesson;
    protected VocabularyItem $item1;
    protected VocabularyItem $item2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create();

        $topic = VocabularyTopic::create([
            'title' => 'Environment & Climate',
            'slug' => 'environment-climate',
            'status' => ContentStatus::PUBLISHED,
        ]);

        $this->lesson = VocabularyLesson::create([
            'topic_id' => $topic->id,
            'title' => 'Pollution & Global Warming',
            'slug' => 'pollution-global-warming',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $this->item1 = VocabularyItem::create([
            'lesson_id' => $this->lesson->id,
            'word' => 'Emission',
            'part_of_speech' => PartOfSpeech::NOUN,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'vietnamese_meaning' => 'Khí thải',
            'example_sentence' => 'Carbon emissions have increased dramatically.',
            'order_index' => 1,
        ]);

        $this->item2 = VocabularyItem::create([
            'lesson_id' => $this->lesson->id,
            'word' => 'Renewable',
            'part_of_speech' => PartOfSpeech::ADJECTIVE,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'vietnamese_meaning' => 'Có thể tái tạo',
            'example_sentence' => 'Solar power is a renewable energy source.',
            'order_index' => 2,
        ]);
    }

    public function test_student_can_mark_word_status_and_update_progress(): void
    {
        // 1. Mark first word as learning
        $response = $this->actingAs($this->student)
            ->post(route('student.vocabulary.items.status', $this->item1), [
                'status' => 'learning',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('student_vocabulary_reviews', [
            'user_id' => $this->student->id,
            'vocabulary_item_id' => $this->item1->id,
            'status' => WordStudyStatus::LEARNING->value,
        ]);

        // Progress should now be in progress (1/2 = 50%)
        $progress = StudentLessonProgress::where('user_id', $this->student->id)
            ->where('lesson_id', $this->lesson->id)
            ->first();

        $this->assertNotNull($progress);
        $this->assertEquals(1, $progress->completed_items_count);
        $this->assertEquals(50, $progress->progress_percentage);
        $this->assertEquals(LearningStatus::IN_PROGRESS, $progress->status);

        // 2. Mark second word as known
        $this->actingAs($this->student)
            ->post(route('student.vocabulary.items.status', $this->item2), [
                'status' => 'known',
            ]);

        $progress->refresh();
        $this->assertEquals(2, $progress->completed_items_count);
        $this->assertEquals(100, $progress->progress_percentage);
        $this->assertEquals(LearningStatus::COMPLETED, $progress->status);
    }

    public function test_student_can_toggle_favorite_word(): void
    {
        // Toggle on
        $response = $this->actingAs($this->student)
            ->post(route('student.vocabulary.items.favorite', $this->item1));

        $response->assertRedirect();

        $this->assertDatabaseHas('student_vocabulary_reviews', [
            'user_id' => $this->student->id,
            'vocabulary_item_id' => $this->item1->id,
            'is_favorite' => true,
        ]);

        // Toggle off
        $this->actingAs($this->student)
            ->post(route('student.vocabulary.items.favorite', $this->item1));

        $this->assertDatabaseHas('student_vocabulary_reviews', [
            'user_id' => $this->student->id,
            'vocabulary_item_id' => $this->item1->id,
            'is_favorite' => false,
        ]);
    }

    public function test_student_can_save_personal_note_for_word(): void
    {
        $response = $this->actingAs($this->student)
            ->post(route('student.vocabulary.items.note', $this->item1), [
                'note' => 'Nhớ dùng kèm collocations: cut/reduce carbon emissions trong Task 2',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('student_vocabulary_reviews', [
            'user_id' => $this->student->id,
            'vocabulary_item_id' => $this->item1->id,
            'personal_note' => 'Nhớ dùng kèm collocations: cut/reduce carbon emissions trong Task 2',
        ]);
    }
}
