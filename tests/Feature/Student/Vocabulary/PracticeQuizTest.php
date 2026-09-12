<?php

namespace Tests\Feature\Student\Vocabulary;

use App\Enums\ContentStatus;
use App\Enums\PartOfSpeech;
use App\Enums\UserRole;
use App\Enums\VocabularyLevel;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyPracticeAnswer;
use App\Models\VocabularyPracticeSession;
use App\Models\VocabularyTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PracticeQuizTest extends TestCase
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
            'title' => 'Technology & AI',
            'slug' => 'technology-ai',
            'status' => ContentStatus::PUBLISHED,
        ]);

        $this->lesson = VocabularyLesson::create([
            'topic_id' => $topic->id,
            'title' => 'Digital Transformation & Automation',
            'slug' => 'digital-transformation-automation',
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $this->item1 = VocabularyItem::create([
            'lesson_id' => $this->lesson->id,
            'word' => 'Automation',
            'part_of_speech' => PartOfSpeech::NOUN,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'vietnamese_meaning' => 'Tự động hóa',
            'example_sentence' => 'Automation leads to increased efficiency in manufacturing.',
            'order_index' => 1,
        ]);

        $this->item2 = VocabularyItem::create([
            'lesson_id' => $this->lesson->id,
            'word' => 'Obsolete',
            'part_of_speech' => PartOfSpeech::ADJECTIVE,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'vietnamese_meaning' => 'Lỗi thời, không còn dùng',
            'example_sentence' => 'New technology makes old equipment obsolete.',
            'order_index' => 2,
        ]);
    }

    public function test_student_can_start_practice_session(): void
    {
        $response = $this->actingAs($this->student)
            ->post(route('student.vocabulary.practice.start', $this->lesson));

        $session = VocabularyPracticeSession::where('user_id', $this->student->id)
            ->where('lesson_id', $this->lesson->id)
            ->first();

        $this->assertNotNull($session);
        $this->assertEquals(2, $session->total_questions);
        $this->assertEquals('in_progress', $session->status);
        $response->assertRedirect(route('student.vocabulary.practice.show', $session));
    }

    public function test_student_can_view_practice_quiz(): void
    {
        $this->actingAs($this->student)
            ->post(route('student.vocabulary.practice.start', $this->lesson));

        $session = VocabularyPracticeSession::where('user_id', $this->student->id)->first();

        $response = $this->actingAs($this->student)
            ->get(route('student.vocabulary.practice.show', $session));

        $response->assertOk();
        $response->assertSee('Luyện tập Từ vựng');
        $response->assertSee('Nộp bài');
    }

    public function test_student_can_submit_practice_answers_and_get_evaluation(): void
    {
        $this->actingAs($this->student)
            ->post(route('student.vocabulary.practice.start', $this->lesson));

        $session = VocabularyPracticeSession::where('user_id', $this->student->id)->first();
        $answers = $session->answers;

        // Simulate 1 correct and 1 incorrect answer
        $postData = [
            'answers' => [
                $answers[0]->id => $answers[0]->correct_answer,
                $answers[1]->id => 'wrong_answer_xyz',
            ],
        ];

        $response = $this->actingAs($this->student)
            ->post(route('student.vocabulary.practice.submit', $session), $postData);

        $session->refresh();
        $this->assertEquals('completed', $session->status);
        $this->assertEquals(1, $session->score);
        $this->assertEquals(50, $session->accuracy_rate);
        $this->assertNotNull($session->completed_at);

        $response->assertRedirect(route('student.vocabulary.practice.result', $session));

        // Result page review
        $resultRes = $this->actingAs($this->student)
            ->get(route('student.vocabulary.practice.result', $session));

        $resultRes->assertOk();
        $resultRes->assertSee('50%');
        $resultRes->assertSee('1 / 2 câu đúng');
    }

    public function test_student_cannot_resubmit_completed_practice_session(): void
    {
        $session = VocabularyPracticeSession::create([
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'session_type' => 'lesson_quiz',
            'status' => 'completed',
            'total_questions' => 2,
            'score' => 2,
            'accuracy_rate' => 100,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('student.vocabulary.practice.submit', $session), [
                'answers' => [],
            ]);

        $response->assertRedirect(route('student.vocabulary.practice.result', $session));
        $response->assertSessionHas('warning');
    }
}
