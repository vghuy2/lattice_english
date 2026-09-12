<?php

namespace Tests\Feature\E2E;

use App\Enums\ContentStatus;
use App\Enums\IeltsType;
use App\Enums\LearningStatus;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Enums\WordStudyStatus;
use App\Enums\WritingTaskType;
use App\Models\StudentLessonProgress;
use App\Models\StudentVocabularyReview;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyPracticeSession;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompleteLearningFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_end_to_end_student_and_admin_workflow(): void
    {
        // 1. Seed complete academic content & rules
        $this->seed(DatabaseSeeder::class);

        // 2. Student Registration & Onboarding Journey
        $studentEmail = 'newlearner@ielts.vn';
        $registerResponse = $this->post(route('register.store'), [
            'name' => 'Nguyễn Văn Học',
            'email' => $studentEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $registerResponse->assertRedirect(route('onboarding.index'));

        $newStudent = User::where('email', $studentEmail)->first();
        $this->assertNotNull($newStudent);
        $this->assertFalse($newStudent->hasCompletedOnboarding());

        // Complete onboarding
        $onboardingResponse = $this->actingAs($newStudent)->post(route('onboarding.store'), [
            'test_type' => IeltsType::ACADEMIC->value,
            'current_band' => 4.5,
            'target_band' => 6.5,
            'target_date' => now()->addMonths(6)->toDateString(),
            'study_days_per_week' => 5,
            'study_goal' => 'Mục tiêu đạt 6.5 Writing và mở rộng vốn từ vựng học thuật trong 6 tháng.',
        ]);
        $onboardingResponse->assertRedirect(route('student.dashboard'));
        $newStudent->refresh();
        $this->assertTrue($newStudent->hasCompletedOnboarding());

        // 3. Access Student Dashboard
        $dashboardResponse = $this->actingAs($newStudent)->get(route('student.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Nguyễn Văn Học');
        $dashboardResponse->assertSee('6.5');

        // 4. Vocabulary Learning Journey
        $topic = VocabularyTopic::where('slug', 'education-academic-life')->first();
        $this->assertNotNull($topic);
        $lesson = $topic->lessons()->first();
        $this->assertNotNull($lesson);

        // View Lesson detail
        $lessonResponse = $this->actingAs($newStudent)->get(route('student.vocabulary.lessons.show', $lesson));
        $lessonResponse->assertStatus(200);

        // Mark a word as Favorite and Known
        $item = $lesson->items()->first();
        $this->assertNotNull($item);

        $favResponse = $this->actingAs($newStudent)->postJson(route('student.vocabulary.items.favorite', $item));
        $favResponse->assertJson(['success' => true, 'is_favorite' => true]);

        $statusResponse = $this->actingAs($newStudent)->postJson(route('student.vocabulary.items.status', $item), [
            'status' => WordStudyStatus::KNOWN->value,
        ]);
        $statusResponse->assertJson(['success' => true]);

        // Start Quiz Practice Session
        $quizStartResponse = $this->actingAs($newStudent)->post(route('student.vocabulary.practice.start', $lesson));
        $quizStartResponse->assertRedirect();
        
        $session = VocabularyPracticeSession::where('user_id', $newStudent->id)->where('lesson_id', $lesson->id)->first();
        $this->assertNotNull($session);
        $this->assertEquals('in_progress', $session->status);

        // Submit Quiz
        $quizSubmitResponse = $this->actingAs($newStudent)->post(route('student.vocabulary.practice.submit', $session), [
            'answers' => [
                $item->id => $item->vietnamese_meaning,
            ],
        ]);
        $quizSubmitResponse->assertRedirect(route('student.vocabulary.practice.result', $session));
        $session->refresh();
        $this->assertEquals('completed', $session->status);

        // 5. Writing Practice & Rule-based Scoring Engine Journey (ZERO AI)
        $prompt = WritingPrompt::where('task_type', WritingTaskType::TASK_1)->first();
        $this->assertNotNull($prompt);

        // Enter Writing Exam Room
        $examRoomResponse = $this->actingAs($newStudent)->get(route('student.writing.practice', $prompt));
        $examRoomResponse->assertStatus(200);

        // Auto-save draft
        $draftResponse = $this->actingAs($newStudent)->post(route('student.writing.draft', $prompt), [
            'essay_content' => 'The chart illustrates the changes in population...',
            'time_spent_seconds' => 120,
        ]);
        $draftResponse->assertJson(['success' => true]);

        // Submit Full Essay
        $essayText = "The line chart illustrates the percentage of elderly citizens in three countries from 1940 to 2040.\n\nOverall, it is clear that all three nations will experience an upward trend in their ageing populations. In addition, Japan starts as the lowest but is predicted to become the highest by the end of the period.\n\nIn 1940, the proportion of people aged 65 and over in the USA was 9%, whereas Sweden had around 7% and Japan was only 5%. Over the next several decades, both the USA and Sweden increased steadily to reach approximately 15% and 14% respectively by 1980.\n\nBy contrast, Japan remained relatively stable at around 4% until 2000. However, after 2030, the figure for Japan is expected to increase rapidly to nearly 27%, surpassing both Sweden at 25% and the USA at 23%. In conclusion, elderly population growth is a prominent demographic feature across all countries.";

        $submitResponse = $this->actingAs($newStudent)->post(route('student.writing.submit', $prompt), [
            'essay_content' => $essayText,
            'time_spent_seconds' => 1100,
        ]);
        $submitResponse->assertRedirect();

        $submission = WritingSubmission::where('user_id', $newStudent->id)
            ->where('writing_prompt_id', $prompt->id)
            ->first();
        $this->assertNotNull($submission);
        $this->assertEquals(SubmissionStatus::GRADED, $submission->status);
        $this->assertNotNull($submission->overall_score);
        $this->assertNotNull($submission->ta_score);
        $this->assertNotNull($submission->cc_score);
        $this->assertNotNull($submission->lr_score);
        $this->assertNotNull($submission->gra_score);
        $this->assertNotEmpty($submission->scoring_breakdown);
        $this->assertNotEmpty($submission->feedback_notes);

        // Student views detailed submission report
        $reportResponse = $this->actingAs($newStudent)->get(route('student.writing.submissions.show', $submission));
        $reportResponse->assertStatus(200);
        $reportResponse->assertSee((string) number_format($submission->overall_score, 1));

        // 6. Admin Management & Manual Re-scoring Journey
        $admin = User::where('email', 'admin@ielts.vn')->first();
        $this->assertNotNull($admin);

        // Admin Dashboard
        $adminDashResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $adminDashResponse->assertStatus(200);

        // Admin Submissions List
        $adminSubmissionsResponse = $this->actingAs($admin)->get(route('admin.writing.submissions.index'));
        $adminSubmissionsResponse->assertStatus(200);
        $adminSubmissionsResponse->assertSee($newStudent->name);

        // Admin Views Submission Detail & Updates Feedback
        $adminDetailResponse = $this->actingAs($admin)->get(route('admin.writing.submissions.show', $submission));
        $adminDetailResponse->assertStatus(200);

        $feedbackUpdateResponse = $this->actingAs($admin)->put(route('admin.writing.submissions.feedback', $submission), [
            'overall_score' => 6.5,
            'ta_score' => 6.5,
            'cc_score' => 6.5,
            'lr_score' => 7.0,
            'gra_score' => 6.0,
            'feedback_notes' => 'Nhận xét từ Giảng viên: Bài viết có cấu trúc rõ ràng, diễn đạt mạch lạc.',
        ]);
        $feedbackUpdateResponse->assertRedirect();

        $submission->refresh();
        $this->assertStringContainsString('Nhận xét từ Giảng viên', $submission->feedback_notes);
        $this->assertEquals(6.5, $submission->overall_score);

        // Admin Re-scores Submission
        $rescoreResponse = $this->actingAs($admin)->post(route('admin.writing.submissions.rescore', $submission));
        $rescoreResponse->assertRedirect();
        $this->assertEquals(SubmissionStatus::GRADED, $submission->status);
    }
}
