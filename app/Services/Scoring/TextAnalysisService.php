<?php

namespace App\Services\Scoring;

class TextAnalysisService
{
    /**
     * Common English stop words to exclude when analyzing lexical repetition.
     */
    protected const STOP_WORDS = [
        'a', 'about', 'above', 'after', 'again', 'against', 'all', 'am', 'an', 'and', 'any', 'are', 'as', 'at',
        'be', 'because', 'been', 'before', 'being', 'below', 'between', 'both', 'but', 'by',
        'could', 'did', 'do', 'does', 'doing', 'down', 'during',
        'each', 'few', 'for', 'from', 'further', 'had', 'has', 'have', 'having', 'he', 'her', 'here', 'hers',
        'herself', 'him', 'himself', 'his', 'how', 'i', 'if', 'in', 'into', 'is', 'it', 'its', 'itself',
        'just', 'me', 'more', 'most', 'my', 'myself',
        'no', 'nor', 'not', 'now', 'of', 'off', 'on', 'once', 'only', 'or', 'other', 'our', 'ours', 'ourselves', 'out', 'over', 'own',
        'same', 'she', 'should', 'so', 'some', 'such',
        'than', 'that', 'the', 'their', 'theirs', 'them', 'themselves', 'then', 'there', 'these', 'they', 'this', 'those', 'through', 'to', 'too',
        'under', 'until', 'up', 'very',
        'was', 'we', 'were', 'what', 'when', 'where', 'which', 'while', 'who', 'whom', 'why', 'with', 'would',
        'you', 'your', 'yours', 'yourself', 'yourselves',
    ];

    /**
     * Common IELTS Cohesive Devices / Linking Words grouped by functional category.
     */
    protected const LINKING_WORDS = [
        'addition' => [
            'in addition', 'furthermore', 'moreover', 'besides', 'additionally', 'not only', 'also', 'as well as', 'along with',
        ],
        'contrast' => [
            'however', 'on the other hand', 'in contrast', 'nevertheless', 'nonetheless', 'although', 'even though',
            'though', 'despite', 'in spite of', 'whereas', 'while', 'conversely', 'alternatively',
        ],
        'cause_effect' => [
            'therefore', 'as a result', 'consequently', 'thus', 'hence', 'because of', 'due to', 'owing to',
            'as a consequence', 'lead to', 'results in',
        ],
        'sequencing' => [
            'firstly', 'secondly', 'thirdly', 'first of all', 'to begin with', 'next', 'then', 'finally', 'subsequently',
        ],
        'exemplification' => [
            'for example', 'for instance', 'such as', 'to illustrate', 'namely', 'as an example',
        ],
        'overview_conclusion' => [
            'overall', 'in summary', 'in conclusion', 'to sum up', 'it is clear that', 'it is noticeable that',
            'it is evident that', 'it can be seen that', 'in general', 'to conclude',
        ],
    ];

    /**
     * Common Academic Words (AWL sample representative for IELTS Band 5.5 - 7.0+).
     */
    protected const ACADEMIC_WORDS = [
        'significant', 'significantly', 'indicate', 'indicated', 'indicates', 'trend', 'trends',
        'proportion', 'percentage', 'majority', 'minority', 'fluctuate', 'fluctuated', 'fluctuation',
        'increase', 'decrease', 'decline', 'declined', 'grow', 'growth', 'dramatic', 'dramatically',
        'substantial', 'substantially', 'steady', 'steadily', 'slight', 'slightly', 'moderate', 'moderately',
        'illustrate', 'illustrates', 'demonstrate', 'demonstrates', 'depict', 'depicts', 'represent',
        'consequently', 'furthermore', 'nevertheless', 'factor', 'factors', 'impact', 'impacts', 'influence',
        'consume', 'consumption', 'generate', 'generation', 'resource', 'resources', 'environment',
        'environmental', 'economic', 'economy', 'social', 'society', 'technology', 'technological',
        'perspective', 'perspectives', 'approach', 'approaches', 'benefit', 'benefits', 'drawback', 'drawbacks',
        'challenge', 'challenges', 'solution', 'solutions', 'measure', 'measures', 'implement', 'implementation',
        'policy', 'policies', 'government', 'governments', 'individual', 'individuals', 'community', 'communities',
        'contribute', 'contribution', 'crucial', 'essential', 'fundamental', 'vital', 'evident', 'apparent',
    ];

    /**
     * Common informal contractions that should ideally be avoided in academic writing.
     */
    protected const INFORMAL_CONTRACTIONS = [
        "don't", "can't", "won't", "isn't", "aren't", "wasn't", "weren't", "haven't", "hasn't", "hadn't",
        "doesn't", "didn't", "couldn't", "shouldn't", "wouldn't", "it's", "i'm", "they're", "we're", "you're",
        "there's", "that's", "what's", "who's", "let's", "gonna", "wanna", "gotta",
    ];

    /**
     * Subordinating conjunctions and complex sentence marker patterns.
     */
    protected const COMPLEX_MARKERS = [
        'although', 'even though', 'though', 'whereas', 'while', 'because', 'since',
        'if', 'unless', 'provided that', 'so that', 'in order that',
        'which', 'who', 'whom', 'whose', 'where', 'when', 'despite', 'in spite of',
    ];

    /**
     * Analyze full essay text and return structured metrics.
     *
     * @return array<string, mixed>
     */
    public function analyze(string $text): array
    {
        $rawText = trim($text);
        $paragraphs = $this->extractParagraphs($rawText);
        $sentences = $this->extractSentences($rawText);
        $words = $this->extractWords($rawText);
        $wordCount = count($words);
        $uniqueWords = array_unique(array_map('strtolower', $words));
        $uniqueWordCount = count($uniqueWords);

        $ttr = $wordCount > 0 ? round($uniqueWordCount / $wordCount, 4) : 0;

        $wordFrequencies = $this->calculateWordFrequencies($words);
        $overusedWords = $this->detectOverusedWords($wordFrequencies, $wordCount);

        $linkingAnalysis = $this->detectLinkingWords($rawText);
        $complexSentenceAnalysis = $this->analyzeComplexSentences($sentences);
        $contractionsFound = $this->detectContractions($rawText);
        $academicVocabFound = $this->detectAcademicVocabulary($words);
        $capitalizationIssues = $this->detectCapitalizationIssues($sentences);

        $avgSentenceLength = count($sentences) > 0 ? round($wordCount / count($sentences), 1) : 0;

        return [
            'word_count' => $wordCount,
            'character_count' => mb_strlen($rawText),
            'paragraph_count' => count($paragraphs),
            'paragraphs' => $paragraphs,
            'sentence_count' => count($sentences),
            'sentences' => $sentences,
            'avg_sentence_length' => $avgSentenceLength,
            'unique_word_count' => $uniqueWordCount,
            'type_token_ratio' => $ttr,
            'word_frequencies' => $wordFrequencies,
            'overused_words' => $overusedWords,
            'linking_words' => $linkingAnalysis,
            'complex_sentences' => $complexSentenceAnalysis,
            'contractions' => $contractionsFound,
            'academic_words' => $academicVocabFound,
            'capitalization_issues' => $capitalizationIssues,
        ];
    }

    /**
     * Extract non-empty paragraphs.
     *
     * @return array<int, array{text: string, word_count: int}>
     */
    protected function extractParagraphs(string $text): array
    {
        $rawParagraphs = preg_split('/\n+/', $text);
        $result = [];

        foreach ($rawParagraphs as $p) {
            $p = trim($p);
            if (! empty($p)) {
                $words = $this->extractWords($p);
                $result[] = [
                    'text' => $p,
                    'word_count' => count($words),
                ];
            }
        }

        return $result;
    }

    /**
     * Extract sentences using punctuation boundaries.
     *
     * @return array<int, string>
     */
    protected function extractSentences(string $text): array
    {
        if (empty(trim($text))) {
            return [];
        }

        // Split on ., !, ? followed by whitespace or end of string
        $rawSentences = preg_split('/(?<=[.!?])\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $result = [];

        foreach ($rawSentences as $s) {
            $s = trim($s);
            if (! empty($s)) {
                $result[] = $s;
            }
        }

        return $result;
    }

    /**
     * Extract array of words (tokens).
     *
     * @return array<int, string>
     */
    protected function extractWords(string $text): array
    {
        if (empty(trim($text))) {
            return [];
        }

        // Split on whitespace after stripping punctuation from edges
        $rawTokens = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $words = [];

        foreach ($rawTokens as $token) {
            $clean = trim($token, " \t\n\r\0\x0B.,!?:;\"'()[]{}<>-");
            if (! empty($clean)) {
                $words[] = $clean;
            }
        }

        return $words;
    }

    /**
     * Calculate frequency of each word (case-insensitive), excluding stop words.
     *
     * @param array<int, string> $words
     * @return array<string, int>
     */
    protected function calculateWordFrequencies(array $words): array
    {
        $frequencies = [];

        foreach ($words as $w) {
            $lower = strtolower($w);
            if (! in_array($lower, self::STOP_WORDS, true) && mb_strlen($lower) > 2) {
                $frequencies[$lower] = ($frequencies[$lower] ?? 0) + 1;
            }
        }

        arsort($frequencies);

        return $frequencies;
    }

    /**
     * Detect words that appear abnormally often (repetition penalty threshold).
     *
     * @param array<string, int> $frequencies
     * @return array<string, int>
     */
    protected function detectOverusedWords(array $frequencies, int $totalWords): array
    {
        $overused = [];
        $threshold = max(4, (int) round($totalWords * 0.035)); // > 3.5% of essay or > 4 times

        foreach ($frequencies as $word => $count) {
            if ($count >= $threshold) {
                $overused[$word] = $count;
            }
        }

        return $overused;
    }

    /**
     * Detect linking words and cohesive devices in text.
     *
     * @return array{total_count: int, unique_count: int, categories: array<string, array<string, int>>, all_found: array<string, int>}
     */
    protected function detectLinkingWords(string $text): array
    {
        $lowerText = strtolower($text);
        $categoriesFound = [];
        $allFound = [];
        $totalCount = 0;

        foreach (self::LINKING_WORDS as $category => $phrases) {
            $categoriesFound[$category] = [];
            foreach ($phrases as $phrase) {
                // Match word boundary
                $pattern = '/\b' . preg_quote($phrase, '/') . '\b/i';
                $matches = preg_match_all($pattern, $lowerText, $m);
                if ($matches > 0) {
                    $categoriesFound[$category][$phrase] = $matches;
                    $allFound[$phrase] = ($allFound[$phrase] ?? 0) + $matches;
                    $totalCount += $matches;
                }
            }
        }

        return [
            'total_count' => $totalCount,
            'unique_count' => count($allFound),
            'categories' => $categoriesFound,
            'all_found' => $allFound,
        ];
    }

    /**
     * Analyze complexity of sentences based on subordinating markers.
     *
     * @param array<int, string> $sentences
     * @return array{total_sentences: int, complex_count: int, complex_ratio: float, details: array<int, array{sentence: string, markers: array<string>}>}
     */
    protected function analyzeComplexSentences(array $sentences): array
    {
        $complexCount = 0;
        $details = [];

        foreach ($sentences as $sentence) {
            $lower = strtolower($sentence);
            $foundMarkers = [];

            foreach (self::COMPLEX_MARKERS as $marker) {
                if (preg_match('/\b' . preg_quote($marker, '/') . '\b/i', $lower)) {
                    $foundMarkers[] = $marker;
                }
            }

            if (! empty($foundMarkers)) {
                $complexCount++;
            }

            $details[] = [
                'sentence' => $sentence,
                'is_complex' => ! empty($foundMarkers),
                'markers' => $foundMarkers,
            ];
        }

        $total = count($sentences);
        $ratio = $total > 0 ? round($complexCount / $total, 3) : 0.0;

        return [
            'total_sentences' => $total,
            'complex_count' => $complexCount,
            'complex_ratio' => $ratio,
            'details' => $details,
        ];
    }

    /**
     * Detect informal contractions in text.
     *
     * @return array<string, int>
     */
    protected function detectContractions(string $text): array
    {
        $found = [];
        $lower = strtolower($text);

        foreach (self::INFORMAL_CONTRACTIONS as $contraction) {
            $pattern = '/\b' . preg_quote($contraction, '/') . '\b/i';
            $matches = preg_match_all($pattern, $lower, $m);
            if ($matches > 0) {
                $found[$contraction] = $matches;
            }
        }

        return $found;
    }

    /**
     * Detect matched academic and high-band IELTS words.
     *
     * @param array<int, string> $words
     * @return array<string, int>
     */
    protected function detectAcademicVocabulary(array $words): array
    {
        $found = [];

        foreach ($words as $w) {
            $lower = strtolower($w);
            if (in_array($lower, self::ACADEMIC_WORDS, true)) {
                $found[$lower] = ($found[$lower] ?? 0) + 1;
            }
        }

        arsort($found);

        return $found;
    }

    /**
     * Detect sentences that do not start with a capital letter.
     *
     * @param array<int, string> $sentences
     * @return array<int, string>
     */
    protected function detectCapitalizationIssues(array $sentences): array
    {
        $issues = [];

        foreach ($sentences as $s) {
            $clean = trim($s, " \"'([");
            if (! empty($clean)) {
                $firstChar = mb_substr($clean, 0, 1);
                if (ctype_lower($firstChar)) {
                    $issues[] = $s;
                }
            }
        }

        return $issues;
    }
}
