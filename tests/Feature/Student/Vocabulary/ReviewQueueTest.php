<?php

namespace Tests\Feature\Student\Vocabulary;

use App\Enums\ContentStatus;
use App\Enums\PartOfSpeech;
use App\Enums\UserRole;
use App\Enums\VocabularyLevel;
use App\Enums\WordStudyStatus;
use App\Models\StudentVocabularyReview;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewQueueTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected VocabularyItem $dueItem;
    protected VocabularyItem $favoriteItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create();

        $topic = VocabularyTopic::create([
            'title' => 'Health & Medicine',
            'slug' => 'health-medicine',
            'status' => ContentStatus::PUBLISHED,
        ]);

        $lesson = VocabularyLesson::create([
            'topic_id' => $topic->id,
            'title' => 'Public Health & Nutrition',
            'slug' => 'public-health-nutrition',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $this->dueItem = VocabularyItem::create([
            'lesson_id' => $lesson->id,
            'word' => 'Epidemic',
            'part_of_speech' => PartOfSpeech::NOUN,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'vietnamese_meaning' => 'Dịch bệnh',
            'example_sentence' => 'The city took measures to prevent an epidemic.',
            'order_index' => 1,
        ]);

        $this->favoriteItem = VocabularyItem::create([
            'lesson_id' => $lesson->id,
            'word' => 'Nutrient',
            'part_of_speech' => PartOfSpeech::NOUN,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'vietnamese_meaning' => 'Chất dinh dưỡng',
            'example_sentence' => 'Vegetables provide essential nutrients.',
            'order_index' => 2,
        ]);

        // Create review for dueItem (due today)
        StudentVocabularyReview::create([
            'user_id' => $this->student->id,
            'vocabulary_item_id' => $this->dueItem->id,
            'status' => WordStudyStatus::LEARNING,
            'next_review_at' => now()->subHour(),
            'mastery_level' => 1,
            'is_favorite' => false,
        ]);

        // Create review for favoriteItem (favorite, not due yet)
        StudentVocabularyReview::create([
            'user_id' => $this->student->id,
            'vocabulary_item_id' => $this->favoriteItem->id,
            'status' => WordStudyStatus::KNOWN,
            'next_review_at' => now()->addDays(5),
            'mastery_level' => 3,
            'is_favorite' => true,
        ]);
    }

    public function test_student_can_view_review_queue_and_tabs(): void
    {
        // 1. Due tab
        $resDue = $this->actingAs($this->student)
            ->get(route('student.vocabulary.review.index', ['tab' => 'due']));

        $resDue->assertOk();
        $resDue->assertSee('Epidemic');
        $resDue->assertDontSee('Nutrient');

        // 2. Favorites tab
        $resFav = $this->actingAs($this->student)
            ->get(route('student.vocabulary.review.index', ['tab' => 'favorites']));

        $resFav->assertOk();
        $resFav->assertSee('Nutrient');
        $resFav->assertDontSee('Epidemic');

        // 3. All tab
        $resAll = $this->actingAs($this->student)
            ->get(route('student.vocabulary.review.index', ['tab' => 'all']));

        $resAll->assertOk();
        $resAll->assertSee('Epidemic');
        $resAll->assertSee('Nutrient');
    }

    public function test_student_can_start_quick_review_quiz_from_due_words(): void
    {
        $response = $this->actingAs($this->student)
            ->post(route('student.vocabulary.review.quiz'));

        $response->assertRedirect();
        
        // Session created with due word
        $this->assertDatabaseHas('vocabulary_practice_sessions', [
            'user_id' => $this->student->id,
            'session_type' => 'review_quiz',
            'status' => 'in_progress',
        ]);
    }
}
