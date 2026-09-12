<?php

namespace Tests\Feature\Seeders;

use App\Enums\ContentStatus;
use App\Enums\LearningStatus;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Enums\WordStudyStatus;
use App\Enums\WritingTaskType;
use App\Models\SampleEssay;
use App\Models\ScoringRubric;
use App\Models\ScoringRule;
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

class AcademicContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_all_academic_and_demo_content(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 1. Verify Users
        $admin = User::where('email', 'admin@ielts.vn')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());

        $student = User::where('email', 'student@ielts.vn')->first();
        $this->assertNotNull($student);
        $this->assertTrue($student->isStudent());

        // 2. Verify Vocabulary Topics, Lessons, and Items
        $this->assertGreaterThanOrEqual(4, VocabularyTopic::count());
        $this->assertGreaterThanOrEqual(5, VocabularyLesson::count());
        $this->assertGreaterThanOrEqual(10, VocabularyItem::count());

        $eduTopic = VocabularyTopic::where('slug', 'education-academic-life')->first();
        $this->assertNotNull($eduTopic);
        $this->assertEquals(ContentStatus::PUBLISHED, $eduTopic->status);
        $this->assertNotEmpty($eduTopic->lessons);

        $curriculumItem = VocabularyItem::where('word', 'curriculum')->first();
        $this->assertNotNull($curriculumItem);
        $this->assertNotEmpty($curriculumItem->ipa);
        $this->assertNotEmpty($curriculumItem->collocations);
        $this->assertNotEmpty($curriculumItem->example_sentence);
        $this->assertNotEmpty($curriculumItem->example_sentence_vi);

        // 3. Verify Writing Prompts, Rubrics, Rules, and Samples
        $this->assertGreaterThanOrEqual(2, WritingPrompt::count());
        $this->assertGreaterThanOrEqual(10, ScoringRubric::count());
        $this->assertGreaterThanOrEqual(5, ScoringRule::count());
        $this->assertGreaterThanOrEqual(3, SampleEssay::count());

        $lineGraphPrompt = WritingPrompt::where('slug', 'proportion-population-aged-65-and-over')->first();
        $this->assertNotNull($lineGraphPrompt);
        $this->assertEquals(WritingTaskType::TASK_1, $lineGraphPrompt->task_type);
        $this->assertNotEmpty($lineGraphPrompt->sampleEssays);

        // 4. Verify Demo Student Activity
        $reviews = StudentVocabularyReview::where('user_id', $student->id)->get();
        $this->assertNotEmpty($reviews);
        $this->assertTrue($reviews->contains('status', WordStudyStatus::REVIEW_NEEDED));
        $this->assertTrue($reviews->contains('status', WordStudyStatus::KNOWN));
        $this->assertTrue($reviews->contains('status', WordStudyStatus::LEARNING));

        $lessonProgress = StudentLessonProgress::where('user_id', $student->id)->get();
        $this->assertNotEmpty($lessonProgress);
        $this->assertTrue($lessonProgress->contains('status', LearningStatus::COMPLETED));

        $practiceSessions = VocabularyPracticeSession::where('user_id', $student->id)->get();
        $this->assertNotEmpty($practiceSessions);
        $firstSession = $practiceSessions->first();
        $this->assertEquals('completed', $firstSession->status);
        $this->assertNotEmpty($firstSession->answers);

        $submissions = WritingSubmission::where('user_id', $student->id)->get();
        $this->assertNotEmpty($submissions);
        $gradedSubmissions = $submissions->where('status', SubmissionStatus::GRADED);
        $this->assertGreaterThanOrEqual(2, $gradedSubmissions->count());

        foreach ($gradedSubmissions as $sub) {
            $this->assertNotNull($sub->overall_score);
            $this->assertNotNull($sub->ta_score);
            $this->assertNotNull($sub->cc_score);
            $this->assertNotNull($sub->lr_score);
            $this->assertNotNull($sub->gra_score);
            $this->assertNotEmpty($sub->scoring_breakdown);
        }
    }
}
