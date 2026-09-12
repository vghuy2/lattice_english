<?php

namespace App\Services\Scoring;

use App\Enums\ScoringCriterion;
use App\Enums\SubmissionStatus;
use App\Enums\WritingTaskType;
use App\Models\ScoringRule;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;

class WritingScoringService
{
    public function __construct(
        protected TextAnalysisService $analysisService
    ) {}

    /**
     * Score a writing submission deterministically and update the database record.
     */
    public function scoreSubmission(WritingSubmission $submission): WritingSubmission
    {
        $prompt = $submission->prompt;
        $content = $submission->essay_content ?? '';

        $analysis = $this->analysisService->analyze($content);
        $scoringResult = $this->evaluate($analysis, $prompt);

        $submission->word_count = $analysis['word_count'];
        $submission->ta_score = $scoringResult['ta_score'];
        $submission->cc_score = $scoringResult['cc_score'];
        $submission->lr_score = $scoringResult['lr_score'];
        $submission->gra_score = $scoringResult['gra_score'];
        $submission->overall_score = $scoringResult['overall_score'];
        $submission->scoring_breakdown = $scoringResult['breakdown'];
        $submission->feedback_notes = $scoringResult['feedback_notes'];
        $submission->status = SubmissionStatus::GRADED;
        $submission->save();

        return $submission;
    }

    /**
     * Evaluate text analysis against active scoring rules and criteria.
     *
     * @param array<string, mixed> $analysis
     * @return array{overall_score: float, ta_score: float, cc_score: float, lr_score: float, gra_score: float, breakdown: array<string, mixed>, feedback_notes: string}
     */
    public function evaluate(array $analysis, WritingPrompt $prompt): array
    {
        $rules = ScoringRule::active()->get()->keyBy('rule_key');

        $taResult = $this->evaluateTaskAchievement($analysis, $prompt, $rules->get('min_word_penalty'), $rules->get('task1_overview_required'));
        $ccResult = $this->evaluateCoherenceCohesion($analysis, $prompt, $rules->get('paragraph_structure_rule'), $rules->get('connective_repetition_threshold'));
        $lrResult = $this->evaluateLexicalResource($analysis, $prompt, $rules->get('lexical_variety_and_repetition'));
        $graResult = $this->evaluateGrammaticalRangeAccuracy($analysis, $prompt, $rules->get('complex_sentence_counter'));

        $taScore = $this->clampScore($taResult['score']);
        $ccScore = $this->clampScore($ccResult['score']);
        $lrScore = $this->clampScore($lrResult['score']);
        $graScore = $this->clampScore($graResult['score']);

        $overallScore = self::calculateOverallBand($taScore, $ccScore, $lrScore, $graScore);

        $breakdown = [
            'metrics' => [
                'word_count' => $analysis['word_count'],
                'target_words' => $prompt->min_words ?? ($prompt->isTask1() ? 150 : 250),
                'paragraph_count' => $analysis['paragraph_count'],
                'sentence_count' => $analysis['sentence_count'],
                'avg_sentence_length' => $analysis['avg_sentence_length'],
                'type_token_ratio' => $analysis['type_token_ratio'],
                'complex_sentence_ratio' => $analysis['complex_sentences']['complex_ratio'],
                'linking_words_count' => $analysis['linking_words']['total_count'],
                'unique_linking_words' => $analysis['linking_words']['unique_count'],
                'academic_words_count' => count($analysis['academic_words']),
                'contractions_count' => count($analysis['contractions']),
            ],
            'criteria' => [
                'ta' => $taResult,
                'cc' => $ccResult,
                'lr' => $lrResult,
                'gra' => $graResult,
            ],
            'overall_band' => $overallScore,
        ];

        $feedbackNotes = $this->generateFeedbackNotes($breakdown, $prompt);

        return [
            'overall_score' => $overallScore,
            'ta_score' => $taScore,
            'cc_score' => $ccScore,
            'lr_score' => $lrScore,
            'gra_score' => $graScore,
            'breakdown' => $breakdown,
            'feedback_notes' => $feedbackNotes,
        ];
    }

    /**
     * Evaluate Task Achievement (Task 1) or Task Response (Task 2).
     *
     * @param array<string, mixed> $analysis
     */
    protected function evaluateTaskAchievement(array $analysis, WritingPrompt $prompt, ?ScoringRule $wordRule, ?ScoringRule $overviewRule): array
    {
        $wordCount = $analysis['word_count'];
        $targetWords = $prompt->min_words ?? ($prompt->isTask1() ? 150 : 250);
        $isTask1 = $prompt->isTask1();

        $baseScore = 6.0;
        $penalties = [];
        $bonuses = [];
        $notes = [];

        // 1. Word Count Evaluation
        $ratio = $targetWords > 0 ? ($wordCount / $targetWords) : 1.0;

        if ($ratio < 0.50) {
            $baseScore = 4.0;
            $penalties[] = 'Bài viết quá ngắn (< 50% độ dài quy định), chưa phát triển đầy đủ các ý chính.';
        } elseif ($ratio < 0.75) {
            $baseScore = 4.5;
            $penalties[] = "Độ dài bài viết ({$wordCount} từ) thiếu nhiều so với yêu cầu tối thiểu ({$targetWords} từ).";
        } elseif ($ratio < 0.90) {
            $baseScore = 5.0;
            $penalties[] = "Bài viết ({$wordCount} từ) hơi ngắn hơn mức chuẩn ({$targetWords} từ).";
        } elseif ($ratio >= 1.0) {
            $bonuses[] = "Đạt và vượt mốc độ dài chuẩn ({$wordCount}/{$targetWords} từ).";
        }

        // 2. Paragraph Structure Check for TA
        if ($analysis['paragraph_count'] >= 3) {
            $bonuses[] = 'Có bố cục các đoạn rõ ràng đáp ứng yêu cầu phân đoạn.';
        } else {
            $baseScore = min($baseScore, 5.0);
            $penalties[] = 'Cần phân chia rõ ràng các phần (Mở bài, Thân bài, Kết bài/Overview).';
        }

        // 3. Task 1 Overview check
        $hasOverview = false;
        if ($isTask1) {
            $overviewFound = $analysis['linking_words']['categories']['overview_conclusion'] ?? [];
            $hasOverview = ! empty($overviewFound);

            if (! $hasOverview) {
                $baseScore = min($baseScore, 5.0);
                $penalties[] = 'Task 1 chưa có đoạn/câu Overview tổng quan (ví dụ: Overall, It is clear that...).';
            } else {
                $bonuses[] = 'Có câu/đoạn Overview nêu bật xu hướng tổng thể trong Task 1.';
                if ($baseScore >= 6.0 && $ratio >= 1.0) {
                    $baseScore = 6.5;
                }
            }
        } else {
            // Task 2: check if has conclusion or clear thesis
            $conclusionFound = $analysis['linking_words']['categories']['overview_conclusion'] ?? [];
            if (! empty($conclusionFound) && $baseScore >= 6.0 && $ratio >= 1.0) {
                $baseScore = 6.5;
                $bonuses[] = 'Có kết luận và quan điểm rõ ràng.';
            }
        }

        return [
            'criterion_name' => $isTask1 ? 'Task Achievement' : 'Task Response',
            'score' => $baseScore,
            'word_ratio' => round($ratio, 2),
            'has_overview' => $hasOverview,
            'bonuses' => $bonuses,
            'penalties' => $penalties,
        ];
    }

    /**
     * Evaluate Coherence & Cohesion (CC).
     *
     * @param array<string, mixed> $analysis
     */
    protected function evaluateCoherenceCohesion(array $analysis, WritingPrompt $prompt, ?ScoringRule $paragraphRule, ?ScoringRule $linkingRule): array
    {
        $paragraphs = $analysis['paragraph_count'];
        $linkingCount = $analysis['linking_words']['total_count'];
        $uniqueLinking = $analysis['linking_words']['unique_count'];
        $categoriesCount = count(array_filter($analysis['linking_words']['categories'], fn ($arr) => ! empty($arr)));

        $baseScore = 5.5;
        $penalties = [];
        $bonuses = [];

        // 1. Paragraphing Rule
        if ($paragraphs === 1) {
            $baseScore = 4.0;
            $penalties[] = 'Bài viết không chia đoạn (viết thành 1 khối duy nhất), vi phạm nghiêm trọng tiêu chí phân đoạn.';
        } elseif ($paragraphs === 2) {
            $baseScore = 5.0;
            $penalties[] = 'Bài viết chỉ có 2 đoạn, phân bổ nội dung chưa cân đối.';
        } elseif ($paragraphs >= 3 && $paragraphs <= 5) {
            $bonuses[] = "Phân chia đoạn hợp lý ({$paragraphs} đoạn văn).";
            $baseScore = 6.0;
        } else {
            $penalties[] = "Chia quá nhiều đoạn ngắn ({$paragraphs} đoạn) làm mạch văn bị vụn.";
            $baseScore = 5.5;
        }

        // 2. Linking Devices Evaluation
        if ($uniqueLinking === 0) {
            $baseScore = min($baseScore, 4.5);
            $penalties[] = 'Chưa sử dụng các từ nối (linking devices) để kết nối các câu và đoạn.';
        } elseif ($uniqueLinking >= 1 && $uniqueLinking <= 3) {
            $penalties[] = 'Số lượng từ nối còn ít và cơ bản, cần đa dạng hóa thêm.';
            $baseScore = min($baseScore, 5.5);
        } elseif ($uniqueLinking >= 4 && $categoriesCount >= 2) {
            $bonuses[] = "Sử dụng từ nối đa dạng ({$uniqueLinking} từ nối thuộc {$categoriesCount} nhóm chức năng).";
            if ($baseScore >= 6.0) {
                $baseScore = 6.5;
            }
        }

        // 3. Connective repetition check
        foreach ($analysis['linking_words']['all_found'] as $phrase => $count) {
            if ($count > 3) {
                $penalties[] = "Từ nối '{$phrase}' bị lặp lại quá nhiều lần ({$count} lần).";
                $baseScore -= 0.5;
            }
        }

        return [
            'criterion_name' => 'Coherence & Cohesion',
            'score' => $baseScore,
            'paragraph_count' => $paragraphs,
            'linking_count' => $linkingCount,
            'unique_linking' => $uniqueLinking,
            'bonuses' => $bonuses,
            'penalties' => $penalties,
        ];
    }

    /**
     * Evaluate Lexical Resource (LR).
     *
     * @param array<string, mixed> $analysis
     */
    protected function evaluateLexicalResource(array $analysis, WritingPrompt $prompt, ?ScoringRule $lexicalRule): array
    {
        $ttr = $analysis['type_token_ratio'];
        $academicCount = count($analysis['academic_words']);
        $overusedCount = count($analysis['overused_words']);
        $contractionsCount = count($analysis['contractions']);

        $baseScore = 5.0;
        $penalties = [];
        $bonuses = [];

        // 1. TTR (Lexical Diversity)
        if ($ttr < 0.35) {
            $baseScore = 4.5;
            $penalties[] = 'Độ phong phú từ vựng thấp (TTR < 0.35), lặp lại nhiều từ đơn điệu.';
        } elseif ($ttr < 0.45) {
            $baseScore = 5.0;
            $penalties[] = 'Vốn từ vựng ở mức cơ bản, cần hạn chế lặp từ.';
        } elseif ($ttr < 0.55) {
            $baseScore = 6.0;
            $bonuses[] = "Độ đa dạng từ vựng tốt (TTR = {$ttr}).";
        } else {
            $baseScore = 6.5;
            $bonuses[] = "Vốn từ vựng phong phú và linh hoạt (TTR = {$ttr}).";
        }

        // 2. Academic & Topic Vocabulary Bonus
        if ($academicCount >= 4) {
            $bonuses[] = "Đã vận dụng {$academicCount} từ vựng học thuật (AWL).";
            if ($baseScore >= 6.0) {
                $baseScore = min(7.5, $baseScore + 0.5);
            }
        }

        // 3. Overused Words Penalty
        if ($overusedCount >= 3) {
            $wordsList = implode(', ', array_keys(array_slice($analysis['overused_words'], 0, 3)));
            $penalties[] = "Một số từ bị lặp lại quá thường xuyên ({$wordsList}). Hãy sử dụng từ đồng nghĩa (synonyms).";
            $baseScore -= 0.5;
        }

        // 4. Informal Contractions Warning
        if ($contractionsCount > 0) {
            $cList = implode(', ', array_keys($analysis['contractions']));
            $penalties[] = "Xuất hiện dạng viết tắt thân mật ({$cList}). Nên viết dạng đầy đủ trong IELTS Writing (e.g. 'do not' thay vì 'don\'t').";
        }

        return [
            'criterion_name' => 'Lexical Resource',
            'score' => $baseScore,
            'type_token_ratio' => $ttr,
            'academic_words_count' => $academicCount,
            'bonuses' => $bonuses,
            'penalties' => $penalties,
        ];
    }

    /**
     * Evaluate Grammatical Range & Accuracy (GRA).
     *
     * @param array<string, mixed> $analysis
     */
    protected function evaluateGrammaticalRangeAccuracy(array $analysis, WritingPrompt $prompt, ?ScoringRule $complexRule): array
    {
        $complexRatio = $analysis['complex_sentences']['complex_ratio'];
        $avgSentenceLength = $analysis['avg_sentence_length'];
        $capIssues = count($analysis['capitalization_issues']);

        $baseScore = 5.0;
        $penalties = [];
        $bonuses = [];

        // 1. Complex Sentence Ratio
        if ($complexRatio < 0.15) {
            $baseScore = 4.5;
            $penalties[] = 'Chủ yếu sử dụng câu đơn giản (< 15% câu phức). Cần áp dụng thêm mệnh đề quan hệ và liên từ phụ thuộc.';
        } elseif ($complexRatio < 0.30) {
            $baseScore = 5.5;
            $bonuses[] = 'Có bắt đầu kết hợp câu ghép và câu phức nhưng tỉ lệ chưa cao.';
        } elseif ($complexRatio < 0.45) {
            $baseScore = 6.0;
            $bonuses[] = "Cấu trúc câu đa dạng tốt ({$analysis['complex_sentences']['complex_count']} câu phức, chiếm " . round($complexRatio * 100) . '%).';
        } else {
            $baseScore = 6.5;
            $bonuses[] = 'Sử dụng rất linh hoạt và phong phú các dạng câu phức tạp (chiếm ' . round($complexRatio * 100) . '%).';
        }

        // 2. Sentence Length Balance
        if ($avgSentenceLength < 10) {
            $penalties[] = 'Độ dài trung bình câu hơi ngắn (< 10 từ/câu), câu văn có xu hướng rời rạc.';
        } elseif ($avgSentenceLength > 30) {
            $penalties[] = 'Một số câu quá dài (> 30 từ/câu) dễ gây lỗi ngữ pháp hoặc làm ý tứ khó theo dõi.';
        }

        // 3. Capitalization & Basic Mechanics
        if ($capIssues > 0) {
            $penalties[] = "Phát hiện {$capIssues} câu chưa viết hoa chữ cái đầu câu.";
            $baseScore -= 0.5;
        }

        return [
            'criterion_name' => 'Grammatical Range and Accuracy',
            'score' => $baseScore,
            'complex_ratio' => $complexRatio,
            'avg_sentence_length' => $avgSentenceLength,
            'bonuses' => $bonuses,
            'penalties' => $penalties,
        ];
    }

    /**
     * Clamp score within IELTS official 0.5 step boundaries (between 3.5 and 9.0).
     */
    protected function clampScore(float $score): float
    {
        $rounded = round($score * 2) / 2;
        return max(3.5, min(9.0, $rounded));
    }

    /**
     * Standard IELTS overall band rounding algorithm.
     */
    public static function calculateOverallBand(float $ta, float $cc, float $lr, float $gra): float
    {
        $avg = ($ta + $cc + $lr + $gra) / 4.0;
        $fraction = $avg - floor($avg);

        if ($fraction < 0.25) {
            $band = floor($avg);
        } elseif ($fraction < 0.75) {
            $band = floor($avg) + 0.5;
        } else {
            $band = floor($avg) + 1.0;
        }

        return max(3.5, min(9.0, (float) $band));
    }

    /**
     * Generate comprehensive structured feedback notes for the student.
     *
     * @param array<string, mixed> $breakdown
     */
    protected function generateFeedbackNotes(array $breakdown, WritingPrompt $prompt): string
    {
        $metrics = $breakdown['metrics'];
        $overall = number_format($breakdown['overall_band'], 1);
        $ta = number_format($breakdown['criteria']['ta']['score'], 1);
        $cc = number_format($breakdown['criteria']['cc']['score'], 1);
        $lr = number_format($breakdown['criteria']['lr']['score'], 1);
        $gra = number_format($breakdown['criteria']['gra']['score'], 1);

        $out = "### 📊 Tổng kết Đánh giá IELTS Writing (Band {$overall})\n\n";
        $out .= "- **Task Achievement / Response:** Band {$ta}\n";
        $out .= "- **Coherence & Cohesion:** Band {$cc}\n";
        $out .= "- **Lexical Resource:** Band {$lr}\n";
        $out .= "- **Grammatical Range & Accuracy:** Band {$gra}\n\n";

        $out .= "#### 📈 Thống kê Định lượng:\n";
        $out .= "- **Số từ thực tế:** {$metrics['word_count']} / {$metrics['target_words']} từ mục tiêu\n";
        $out .= "- **Số đoạn văn:** {$metrics['paragraph_count']} đoạn\n";
        $out .= "- **Số câu văn:** {$metrics['sentence_count']} câu (trung bình {$metrics['avg_sentence_length']} từ/câu)\n";
        $out .= "- **Tỷ lệ câu phức:** " . round($metrics['complex_sentence_ratio'] * 100) . "%\n";
        $out .= "- **Từ nối đã nhận diện:** {$metrics['unique_linking_words']} từ khác nhau ({$metrics['linking_words_count']} lượt dùng)\n\n";

        $out .= "#### 💡 Nhận xét chi tiết & Lời khuyên nâng Band:\n";

        foreach ($breakdown['criteria'] as $key => $crit) {
            $cName = $crit['criterion_name'];
            $cScore = number_format($crit['score'], 1);
            $out .= "\n**1. {$cName} (Band {$cScore}):**\n";

            if (! empty($crit['bonuses'])) {
                foreach ($crit['bonuses'] as $b) {
                    $out .= "  - ✅ {$b}\n";
                }
            }
            if (! empty($crit['penalties'])) {
                foreach ($crit['penalties'] as $p) {
                    $out .= "  - ⚠️ {$p}\n";
                }
            }
        }

        $out .= "\n---\n*Hệ thống chấm điểm tự động theo bộ tiêu chí IELTS chính thức (Rule-based Engine, hoàn toàn minh bạch và nhất quán).*";

        return $out;
    }
}
