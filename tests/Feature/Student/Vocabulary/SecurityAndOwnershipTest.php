<?php

namespace Tests\Feature\Student\Vocabulary;

use App\Enums\ContentStatus;
use App\Enums\PartOfSpeech;
use App\Enums\UserRole;
use App\Enums\VocabularyLevel;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyPracticeSession;
use App\Models\VocabularyTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndOwnershipTest extends TestCase
{
    use RefreshDatabase;

    protected User $student1;
    protected User $student2;
    protected VocabularyLesson $lesson;
    protected VocabularyPracticeSession $student1Session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student1 = User::factory()->create();
        $this->student2 = User::factory()->create();

        $topic = VocabularyTopic::create([
            'title' => 'Society & Culture',
            'slug' => 'society-culture',
            'status' => ContentStatus::PUBLISHED,
        ]);

        $this->lesson = VocabularyLesson::create([
            'topic_id' => $topic->id,
            'title' => 'Social Inequality & Welfare',
            'slug' => 'social-inequality-welfare',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $item = VocabularyItem::create([
            'lesson_id' => $this->lesson->id,
            'word' => 'Welfare',
            'part_of_speech' => PartOfSpeech::NOUN,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'vietnamese_meaning' => 'Phúc lợi xã hội',
            'example_sentence' => 'The government introduced new welfare programs.',
            'order_index' => 1,
        ]);

        $this->student1Session = VocabularyPracticeSession::create([
            'user_id' => $this->student1->id,
            'lesson_id' => $this->lesson->id,
            'session_type' => 'lesson_quiz',
            'status' => 'in_progress',
            'total_questions' => 1,
            'score' => 0,
            'accuracy_rate' => 0,
        ]);
    }

    public function test_student_cannot_view_another_students_practice_session(): void
    {
        $response = $this->actingAs($this->student2)
            ->get(route('student.vocabulary.practice.show', $this->student1Session));

        $response->assertForbidden();
    }

    public function test_student_cannot_submit_another_students_practice_session(): void
    {
        $response = $this->actingAs($this->student2)
            ->post(route('student.vocabulary.practice.submit', $this->student1Session), [
                'answers' => [],
            ]);

        $response->assertForbidden();
    }

    public function test_student_cannot_view_another_students_practice_result(): void
    {
        $response = $this->actingAs($this->student2)
            ->get(route('student.vocabulary.practice.result', $this->student1Session));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_when_accessing_vocabulary(): void
    {
        $response = $this->get(route('student.vocabulary.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_onboarded_student_is_redirected_to_onboarding(): void
    {
        $notOnboarded = User::factory()->notOnboarded()->create();

        $response = $this->actingAs($notOnboarded)
            ->get(route('student.vocabulary.index'));

        $response->assertRedirect(route('onboarding.index'));
    }
}
