<?php

namespace Tests\Feature\Scoring;

use App\Enums\ContentStatus;
use App\Enums\SubmissionStatus;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Models\User;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use App\Services\Scoring\WritingScoringService;
use Database\Seeders\WritingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuleBasedScoringEngineTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected WritingPrompt $task1Prompt;
    protected WritingPrompt $task2Prompt;
    protected WritingScoringService $scoringService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WritingSeeder::class);

        $this->scoringService = app(WritingScoringService::class);
        $this->student = User::factory()->create();
        $topic = VocabularyTopic::create([
            'title' => 'Technology & Society',
            'slug' => 'technology-society',
            'status' => ContentStatus::PUBLISHED,
            'order_index' => 1,
        ]);

        $this->task1Prompt = WritingPrompt::create([
            'topic_id' => $topic->id,
            'title' => 'Bar Chart: Internet Usage',
            'slug' => 'bar-chart-internet-usage',
            'task_type' => WritingTaskType::TASK_1,
            'prompt_type' => WritingPromptType::BAR_CHART,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'The bar chart illustrates the percentage of individuals using the internet between 2010 and 2020.',
            'min_words' => 150,
            'time_limit_minutes' => 20,
            'order_index' => 1,
        ]);

        $this->task2Prompt = WritingPrompt::create([
            'topic_id' => $topic->id,
            'title' => 'Essay: Online Education vs Traditional Learning',
            'slug' => 'essay-online-education-vs-traditional-learning',
            'task_type' => WritingTaskType::TASK_2,
            'prompt_type' => WritingPromptType::OPINION,
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
            'prompt_text' => 'Some people think that online learning is better than traditional classrooms. Do you agree or disagree?',
            'min_words' => 250,
            'time_limit_minutes' => 40,
            'order_index' => 2,
        ]);
    }

    public function test_ielts_official_band_rounding_formula(): void
    {
        // Average 5.125 -> Band 5.0 (fraction < 0.25)
        $this->assertEquals(5.0, WritingScoringService::calculateOverallBand(5.0, 5.0, 5.0, 5.5));

        // Average 5.25 -> Band 5.5 (0.25 <= fraction < 0.75)
        $this->assertEquals(5.5, WritingScoringService::calculateOverallBand(5.0, 5.5, 5.0, 5.5));

        // Average 5.75 -> Band 6.0 (fraction >= 0.75)
        $this->assertEquals(6.0, WritingScoringService::calculateOverallBand(6.0, 6.0, 5.5, 5.5));

        // Average 6.375 -> Band 6.5
        $this->assertEquals(6.5, WritingScoringService::calculateOverallBand(6.5, 6.5, 6.0, 6.5));
    }

    public function test_task1_scoring_with_overview_and_proper_structure(): void
    {
        $essay = "The bar chart illustrates the percentage of individuals using the internet across four different countries between the years 2010 and 2020.\n\nOverall, it is clear that there was a significant upward trend in internet usage in all surveyed countries throughout the entire decade. In addition, country A maintained the highest proportion of users in both examined years, while country B started from the lowest point.\n\nIn 2010, approximately 40% of people in country A accessed the internet on a regular basis. Subsequently, this figure increased substantially to reach 85% by the end of 2020. Similarly, country C experienced steady growth, starting from 30% and rising to 65% over the same ten-year period.\n\nHowever, country B began at a much lower percentage of only 20% in 2010, although it also achieved noticeable progress to reach nearly 50% in 2020. In conclusion, internet adoption expanded dramatically across all nations, reflecting widespread technological modernization.";

        $submission = WritingSubmission::create([
            'user_id' => $this->student->id,
            'writing_prompt_id' => $this->task1Prompt->id,
            'essay_content' => $essay,
            'status' => SubmissionStatus::SUBMITTED,
        ]);

        $graded = $this->scoringService->scoreSubmission($submission);

        $this->assertEquals(SubmissionStatus::GRADED, $graded->status);
        $this->assertGreaterThanOrEqual(6.0, $graded->ta_score);
        $this->assertGreaterThanOrEqual(6.0, $graded->cc_score);
        $this->assertGreaterThanOrEqual(6.0, $graded->overall_score);
        $this->assertNotNull($graded->feedback_notes);
        $this->assertNotNull($graded->scoring_breakdown);
    }

    public function test_task1_missing_overview_caps_ta_score_at_band_5_0(): void
    {
        $essay = "The bar chart illustrates the percentage of individuals using the internet in 2010 and 2020.\n\nIn 2010, the figure for country A was 40%, and for country B it was 20%.\n\nIn 2020, country A grew to 85% while country B grew to 60%. Both countries showed growth in data numbers.";

        $submission = WritingSubmission::create([
            'user_id' => $this->student->id,
            'writing_prompt_id' => $this->task1Prompt->id,
            'essay_content' => $essay,
            'status' => SubmissionStatus::SUBMITTED,
        ]);

        $graded = $this->scoringService->scoreSubmission($submission);

        // Without overview, Task Achievement must not exceed 5.0
        $this->assertLessThanOrEqual(5.0, $graded->ta_score);
    }

    public function test_single_paragraph_penalizes_coherence_and_cohesion(): void
    {
        $essay = "Online education has become extremely popular around the world. In my opinion, I believe that online education has many benefits because students can study at their own pace. However, traditional classrooms are also important because students can interact directly with teachers and classmates. Furthermore, practical subjects require physical laboratories and face-to-face demonstrations. Therefore, combining both approaches provides the best educational outcomes.";

        $submission = WritingSubmission::create([
            'user_id' => $this->student->id,
            'writing_prompt_id' => $this->task2Prompt->id,
            'essay_content' => $essay, // Single paragraph without newline breaks
            'status' => SubmissionStatus::SUBMITTED,
        ]);

        $graded = $this->scoringService->scoreSubmission($submission);

        $this->assertLessThanOrEqual(4.5, $graded->cc_score);
        $this->assertStringContainsString('không chia đoạn', $graded->feedback_notes);
    }

    public function test_student_submitting_essay_triggers_instant_grading_via_controller(): void
    {
        $essay = "Nowadays, online education has transformed the way people acquire knowledge across the world.\n\nFirstly, digital learning offers remarkable flexibility. Students can access educational resources anytime, which allows them to balance study and work effectively. In addition, learners can choose courses from prestigious international universities without geographical constraints.\n\nHowever, traditional classrooms remain indispensable because direct human interaction enhances communication skills. Furthermore, group discussions in physical classrooms foster teamwork and emotional intelligence.\n\nIn conclusion, while online learning is highly beneficial, blending online and traditional education is the most effective approach for future learners.";

        $response = $this->actingAs($this->student)
            ->post(route('student.writing.submit', $this->task2Prompt), [
                'essay_content' => $essay,
                'time_spent_seconds' => 2100,
            ]);

        $submission = WritingSubmission::where('user_id', $this->student->id)
            ->where('writing_prompt_id', $this->task2Prompt->id)
            ->first();

        $this->assertNotNull($submission);
        $this->assertEquals(SubmissionStatus::GRADED, $submission->status);
        $this->assertNotNull($submission->overall_score);
        $this->assertNotNull($submission->ta_score);
        $this->assertNotNull($submission->cc_score);
        $this->assertNotNull($submission->lr_score);
        $this->assertNotNull($submission->gra_score);
        $this->assertNotNull($submission->scoring_breakdown);
        $this->assertNotNull($submission->feedback_notes);

        $response->assertRedirect(route('student.writing.submissions.show', $submission));
    }
}
