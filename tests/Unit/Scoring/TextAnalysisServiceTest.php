<?php

namespace Tests\Unit\Scoring;

use App\Services\Scoring\TextAnalysisService;
use PHPUnit\Framework\TestCase;

class TextAnalysisServiceTest extends TestCase
{
    protected TextAnalysisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TextAnalysisService();
    }

    public function test_it_correctly_analyzes_paragraphs_sentences_and_words(): void
    {
        $text = "The line graph illustrates global temperature anomalies between 1900 and 2000.\n\nOverall, there was a steady upward trend in temperatures across the century. In addition, the increase was particularly significant after 1970.\n\nIn conclusion, temperatures reached unprecedented levels.";

        $analysis = $this->service->analyze($text);

        $this->assertEquals(3, $analysis['paragraph_count']);
        $this->assertEquals(4, $analysis['sentence_count']);
        $this->assertGreaterThan(30, $analysis['word_count']);
        $this->assertGreaterThan(0.5, $analysis['type_token_ratio']);
    }

    public function test_it_detects_cohesive_devices_and_linking_words(): void
    {
        $text = "Firstly, education provides essential skills. In addition, it develops critical thinking. However, some people argue that experience is more valuable. Therefore, a balance is needed. In conclusion, both play crucial roles.";

        $analysis = $this->service->analyze($text);

        $this->assertGreaterThanOrEqual(4, $analysis['linking_words']['unique_count']);
        $this->assertArrayHasKey('firstly', $analysis['linking_words']['all_found']);
        $this->assertArrayHasKey('in addition', $analysis['linking_words']['all_found']);
        $this->assertArrayHasKey('however', $analysis['linking_words']['all_found']);
        $this->assertArrayHasKey('therefore', $analysis['linking_words']['all_found']);
        $this->assertArrayHasKey('in conclusion', $analysis['linking_words']['all_found']);
    }

    public function test_it_detects_complex_sentences(): void
    {
        $text = "Although online education is convenient, many students prefer face-to-face interaction because it fosters social skills. Some individuals think that technology has drawbacks which cannot be ignored.";

        $analysis = $this->service->analyze($text);

        $this->assertEquals(2, $analysis['sentence_count']);
        $this->assertEquals(2, $analysis['complex_sentences']['complex_count']);
        $this->assertEquals(1.0, $analysis['complex_sentences']['complex_ratio']);
    }

    public function test_it_detects_informal_contractions(): void
    {
        $text = "I don't believe that we can't solve this problem if it's taken seriously.";

        $analysis = $this->service->analyze($text);

        $this->assertArrayHasKey("don't", $analysis['contractions']);
        $this->assertArrayHasKey("can't", $analysis['contractions']);
        $this->assertArrayHasKey("it's", $analysis['contractions']);
    }

    public function test_it_detects_academic_vocabulary(): void
    {
        $text = "There was a significant proportion of economic growth which had a substantial impact on society.";

        $analysis = $this->service->analyze($text);

        $this->assertArrayHasKey("significant", $analysis['academic_words']);
        $this->assertArrayHasKey("proportion", $analysis['academic_words']);
        $this->assertArrayHasKey("economic", $analysis['academic_words']);
        $this->assertArrayHasKey("substantial", $analysis['academic_words']);
        $this->assertArrayHasKey("impact", $analysis['academic_words']);
        $this->assertArrayHasKey("society", $analysis['academic_words']);
    }
}
