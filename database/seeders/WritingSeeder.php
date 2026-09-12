<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\ScoringCriterion;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Models\SampleEssay;
use App\Models\ScoringRubric;
use App\Models\ScoringRule;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use Illuminate\Database\Seeder;

class WritingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRubrics();
        $this->seedScoringRules();
        $this->seedPromptsAndSampleEssays();
    }

    /**
     * Seed IELTS Band Descriptors (Rubrics).
     */
    protected function seedRubrics(): void
    {
        $rubricsData = [
            // TASK 1 - Task Achievement (TA)
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::TASK_ACHIEVEMENT_RESPONSE,
                'band_score' => 5.0,
                'description' => 'Trình bày thông tin tổng quát nhưng có thể thiếu đoạn Overview rõ ràng hoặc chỉ liệt kê số liệu chi tiết mà không tóm lược xu hướng chính. Có thể chọn lọc dữ liệu chưa thỏa đáng.',
                'key_indicators' => ['overview_quality' => 'weak_or_missing', 'min_word_reached' => true],
            ],
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::TASK_ACHIEVEMENT_RESPONSE,
                'band_score' => 6.0,
                'description' => 'Có đoạn Overview rõ ràng chỉ ra xu hướng chính/sự khác biệt nổi bật. Chọn lọc thông tin phù hợp và có dẫn chứng số liệu, dù một vài chi tiết có thể chưa đầy đủ hoặc hơi máy móc.',
                'key_indicators' => ['overview_quality' => 'clear', 'trend_identified' => true, 'key_features' => true],
            ],
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::TASK_ACHIEVEMENT_RESPONSE,
                'band_score' => 7.0,
                'description' => 'Hoàn thành xuất sắc yêu cầu đề bài. Nêu bật tổng quan rõ nét, chọn lọc và so sánh các đặc điểm nổi bật (key features) một cách logic, dẫn chứng số liệu chính xác và thỏa đáng.',
                'key_indicators' => ['overview_quality' => 'high', 'comparisons_made' => true, 'data_accurate' => true],
            ],

            // TASK 2 - Task Response (TR)
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::TASK_ACHIEVEMENT_RESPONSE,
                'band_score' => 5.0,
                'description' => 'Trả lời được một phần đề bài, lập trường chưa xuyên suốt hoặc luận điểm còn chung chung, thiếu ví dụ và phân tích giải thích hỗ trợ.',
                'key_indicators' => ['position_clear' => false, 'arguments_developed' => 'limited'],
            ],
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::TASK_ACHIEVEMENT_RESPONSE,
                'band_score' => 6.0,
                'description' => 'Trả lời tất cả các phần của đề bài. Nêu rõ quan điểm xuyên suốt bài viết. Các luận điểm chính được trình bày kèm giải thích dù một số ý có thể chưa đào sâu hoặc hơi khái quát.',
                'key_indicators' => ['position_clear' => true, 'all_parts_covered' => true, 'arguments_developed' => 'adequate'],
            ],
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::TASK_ACHIEVEMENT_RESPONSE,
                'band_score' => 7.0,
                'description' => 'Giải quyết trọn vẹn và sâu sắc tất cả các khía cạnh của đề bài. Lập trường rõ ràng và nhất quán. Các luận điểm được mở rộng logic kèm ví dụ minh họa xác đáng.',
                'key_indicators' => ['position_clear' => true, 'arguments_extended' => true, 'examples_relevant' => true],
            ],

            // Coherence & Cohesion (CC)
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::COHERENCE_COHESION,
                'band_score' => 5.0,
                'description' => 'Có phân chia đoạn văn nhưng cấu trúc còn lỏng lẻo. Sử dụng từ nối (linking words) còn lặp lại hoặc thiếu tự nhiên.',
                'key_indicators' => ['paragraphs' => 2, 'linking_variety' => 'low'],
            ],
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::COHERENCE_COHESION,
                'band_score' => 6.0,
                'description' => 'Cấu trúc bài viết logic, chia đoạn hợp lý. Sử dụng từ nối tương đối đa dạng dù đôi chỗ còn máy móc hoặc lặp lại một vài cụm liên kết.',
                'key_indicators' => ['paragraphs' => 3, 'linking_variety' => 'medium'],
            ],
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::COHERENCE_COHESION,
                'band_score' => 7.0,
                'description' => 'Thông tin và ý tưởng sắp xếp rất logic, tiến trình bài viết mượt mà. Phân đoạn chuẩn mực, dùng từ nối và đại từ liên kết linh hoạt, tự nhiên.',
                'key_indicators' => ['paragraphs' => 4, 'linking_variety' => 'high', 'progression_clear' => true],
            ],
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::COHERENCE_COHESION,
                'band_score' => 5.0,
                'description' => 'Có chia đoạn nhưng thiếu câu chủ đề (topic sentence) rõ ràng. Dùng từ nối lặp lại (First, Second, Also, And).',
                'key_indicators' => ['paragraphs' => 3, 'linking_variety' => 'low'],
            ],
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::COHERENCE_COHESION,
                'band_score' => 6.0,
                'description' => 'Mỗi đoạn có câu chủ đề và ý trung tâm rõ ràng. Sử dụng các phương tiện liên kết hiệu quả giữa các câu và các đoạn.',
                'key_indicators' => ['paragraphs' => 4, 'linking_variety' => 'medium', 'clear_topic_sentence' => true],
            ],
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::COHERENCE_COHESION,
                'band_score' => 7.0,
                'description' => 'Mạch lạc xuất sắc, chuyển đoạn mượt mà, sử dụng đa dạng các cấu trúc liên kết nội dung và đại từ thay thế chuẩn xác.',
                'key_indicators' => ['paragraphs' => 4, 'linking_variety' => 'high'],
            ],

            // Lexical Resource (LR)
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::LEXICAL_RESOURCE,
                'band_score' => 5.0,
                'description' => 'Vốn từ vựng cơ bản hạn chế, lặp lại các từ mô tả tăng/giảm đơn giản (increase, decrease). Có lỗi chính tả hoặc dùng sai collocation.',
                'key_indicators' => ['vocab_band' => '4.5_5.0', 'repetition_rate' => 'high'],
            ],
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::LEXICAL_RESOURCE,
                'band_score' => 6.0,
                'description' => 'Vốn từ vựng đủ dùng để miêu tả dữ liệu. Có sử dụng một số từ học thuật và collocations (e.g. sharp rise, steady decline), đôi chỗ còn sai sót nhỏ.',
                'key_indicators' => ['vocab_band' => '5.5_6.0', 'academic_collocations' => 'adequate'],
            ],
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::LEXICAL_RESOURCE,
                'band_score' => 7.0,
                'description' => 'Sử dụng vốn từ phong phú, linh hoạt. Dùng nhiều từ vựng ít phổ biến (less common lexical items) và collocations chuẩn xác, hiếm khi mắc lỗi từ.',
                'key_indicators' => ['vocab_band' => '6.5_plus', 'academic_collocations' => 'rich'],
            ],
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::LEXICAL_RESOURCE,
                'band_score' => 5.0,
                'description' => 'Sử dụng từ vựng ở mức tối thiểu cho chủ đề, thường xuyên lặp từ và mắc lỗi kết hợp từ (collocations).',
                'key_indicators' => ['vocab_band' => '4.5_5.0'],
            ],
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::LEXICAL_RESOURCE,
                'band_score' => 6.0,
                'description' => 'Vốn từ vựng đủ phong phú cho chủ đề nghị luận. Cố gắng sử dụng từ học thuật và paraphrasing dù có một vài lỗi chính tả hoặc chọn từ chưa chuẩn xác.',
                'key_indicators' => ['vocab_band' => '5.5_6.0'],
            ],
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::LEXICAL_RESOURCE,
                'band_score' => 7.0,
                'description' => 'Sử dụng vốn từ vựng phong phú, học thuật với độ chính xác cao. Dùng tự nhiên các collocations nâng cao và từ ngữ chuyên biệt theo chủ đề.',
                'key_indicators' => ['vocab_band' => '6.5_plus'],
            ],

            // Grammatical Range & Accuracy (GRA)
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::GRAMMATICAL_RANGE_ACCURACY,
                'band_score' => 5.0,
                'description' => 'Sử dụng chủ yếu câu đơn. Khi thử viết câu phức thường mắc lỗi chia thì, giới từ hoặc cấu trúc bị động.',
                'key_indicators' => ['complex_sentence_ratio' => '< 20%'],
            ],
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::GRAMMATICAL_RANGE_ACCURACY,
                'band_score' => 6.0,
                'description' => 'Kết hợp cả câu đơn và câu phức. Đa phần các câu truyền đạt đúng ý, có mắc một số lỗi ngữ pháp nhỏ nhưng không gây cản trở người đọc hiểu.',
                'key_indicators' => ['complex_sentence_ratio' => '>= 30%'],
            ],
            [
                'task_type' => WritingTaskType::TASK_1,
                'criterion' => ScoringCriterion::GRAMMATICAL_RANGE_ACCURACY,
                'band_score' => 7.0,
                'description' => 'Sử dụng đa dạng các cấu trúc câu phức tạp (mệnh đề quan hệ, câu bị động, cấu trúc đảo ngữ, mệnh đề rút gọn). Tỉ lệ câu không mắc lỗi ngữ pháp rất cao.',
                'key_indicators' => ['complex_sentence_ratio' => '>= 50%', 'error_free_sentences' => 'high'],
            ],
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::GRAMMATICAL_RANGE_ACCURACY,
                'band_score' => 5.0,
                'description' => 'Dùng cấu trúc câu đơn giản. Lỗi ngữ pháp và dấu câu xuất hiện thường xuyên gây khó hiểu cho người đọc.',
                'key_indicators' => ['complex_sentence_ratio' => '< 20%'],
            ],
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::GRAMMATICAL_RANGE_ACCURACY,
                'band_score' => 6.0,
                'description' => 'Sử dụng linh hoạt câu đơn và câu phức. Có một số lỗi chia động từ, mạo từ hoặc dấu câu nhưng nhìn chung ý tứ rõ ràng.',
                'key_indicators' => ['complex_sentence_ratio' => '>= 30%'],
            ],
            [
                'task_type' => WritingTaskType::TASK_2,
                'criterion' => ScoringCriterion::GRAMMATICAL_RANGE_ACCURACY,
                'band_score' => 7.0,
                'description' => 'Kiểm soát ngữ pháp và dấu câu rất tốt. Sử dụng phong phú cấu trúc phức tạp với độ chính xác cao.',
                'key_indicators' => ['complex_sentence_ratio' => '>= 50%'],
            ],
        ];

        foreach ($rubricsData as $item) {
            ScoringRubric::updateOrCreate(
                [
                    'task_type' => $item['task_type']->value,
                    'criterion' => $item['criterion']->value,
                    'band_score' => $item['band_score'],
                ],
                [
                    'description' => $item['description'],
                    'key_indicators' => $item['key_indicators'],
                ]
            );
        }
    }

    /**
     * Seed Deterministic Rule-Based Scoring Engine Rules.
     */
    protected function seedScoringRules(): void
    {
        $rules = [
            [
                'rule_key' => 'min_word_penalty',
                'name' => 'Quy tắc trừ điểm thiếu số từ tối thiểu',
                'description' => 'Trừ điểm Task Achievement / Task Response nếu bài viết không đạt 150 từ (Task 1) hoặc 250 từ (Task 2).',
                'task_type' => 'all',
                'criterion' => ScoringCriterion::TASK_ACHIEVEMENT_RESPONSE,
                'parameters' => [
                    'task_1_min' => 150,
                    'task_2_min' => 250,
                    'under_10_percent_penalty' => -0.5,
                    'under_25_percent_penalty' => -1.0,
                    'under_50_percent_penalty' => -2.0,
                ],
                'is_active' => true,
            ],
            [
                'rule_key' => 'task1_overview_required',
                'name' => 'Quy tắc bắt buộc có đoạn Overview trong Task 1',
                'description' => 'Nếu bài Task 1 không phát hiện câu/đoạn Overview (Overall, In summary, It is clear that...), band điểm TA tối đa chỉ đạt 5.0.',
                'task_type' => 'task_1',
                'criterion' => ScoringCriterion::TASK_ACHIEVEMENT_RESPONSE,
                'parameters' => [
                    'keywords' => ['overall', 'in general', 'it is clear that', 'it is noticeable that', 'it is evident that', 'in summary'],
                    'max_band_without_overview' => 5.0,
                ],
                'is_active' => true,
            ],
            [
                'rule_key' => 'paragraph_structure_rule',
                'name' => 'Quy tắc cấu trúc phân chia đoạn văn',
                'description' => 'Bài viết IELTS đạt chuẩn bắt buộc có từ 3 đến 5 đoạn văn riêng biệt. Viết 1 đoạn liền mạch sẽ bị hạ band Coherence & Cohesion.',
                'task_type' => 'all',
                'criterion' => ScoringCriterion::COHERENCE_COHESION,
                'parameters' => [
                    'min_paragraphs' => 3,
                    'max_paragraphs' => 5,
                    'single_paragraph_penalty' => -1.5,
                ],
                'is_active' => true,
            ],
            [
                'rule_key' => 'connective_repetition_threshold',
                'name' => 'Quy tắc lặp từ nối và liên từ',
                'description' => 'Nếu một từ nối (e.g. And, But, Because, First, Also) lặp lại quá 3 lần, trừ điểm tiêu chí Coherence & Cohesion.',
                'task_type' => 'all',
                'criterion' => ScoringCriterion::COHERENCE_COHESION,
                'parameters' => [
                    'max_repeat_per_word' => 3,
                    'penalty_per_excess' => -0.5,
                ],
                'is_active' => true,
            ],
            [
                'rule_key' => 'lexical_variety_and_repetition',
                'name' => 'Quy tắc độ phong phú từ vựng (Lexical Diversity)',
                'description' => 'Tính Type-Token Ratio (TTR) và phát hiện lặp lại từ vựng thông thường. Thưởng điểm nếu có collocations học thuật IELTS.',
                'task_type' => 'all',
                'criterion' => ScoringCriterion::LEXICAL_RESOURCE,
                'parameters' => [
                    'min_ttr_band_6' => 0.45,
                    'min_ttr_band_7' => 0.55,
                ],
                'is_active' => true,
            ],
            [
                'rule_key' => 'complex_sentence_counter',
                'name' => 'Quy tắc tỷ lệ câu phức và đa dạng ngữ pháp',
                'description' => 'Đo lường tỷ lệ các câu chứa liên từ phụ thuộc (although, whereas, while, because, which, who, that, if) để xếp band GRA.',
                'task_type' => 'all',
                'criterion' => ScoringCriterion::GRAMMATICAL_RANGE_ACCURACY,
                'parameters' => [
                    'band_5_threshold' => 0.20,
                    'band_6_threshold' => 0.35,
                    'band_7_threshold' => 0.50,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($rules as $r) {
            ScoringRule::updateOrCreate(
                ['rule_key' => $r['rule_key']],
                $r
            );
        }
    }

    /**
     * Seed Writing Prompts & Model Essays.
     */
    protected function seedPromptsAndSampleEssays(): void
    {
        $topicEdu = VocabularyTopic::where('slug', 'education-academic-life')->first();
        $topicEnv = VocabularyTopic::where('slug', 'environment-climate')->first();
        $topicTech = VocabularyTopic::where('slug', 'technology-ai')->first();

        // 1. Task 1: Line Graph (Population 65+)
        $prompt1 = WritingPrompt::updateOrCreate(
            ['slug' => 'proportion-population-aged-65-and-over'],
            [
                'task_type' => WritingTaskType::TASK_1,
                'prompt_type' => WritingPromptType::LINE_GRAPH,
                'topic_id' => $topicTech?->id ?? null,
                'title' => 'Proportion of Population Aged 65 and Over (1940–2040)',
                'prompt_text' => 'The graph below shows the proportion of the population aged 65 and over between 1940 and 2040 in three countries: Japan, Sweden, and the USA. Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
                'level' => VocabularyLevel::BAND_4_5_5_0,
                'min_words' => 150,
                'time_limit_minutes' => 20,
                'guidance' => '1. Đoạn 1 (Intro): Paraphrase lại câu hỏi đề bài.\n2. Đoạn 2 (Overview): Chỉ ra xu hướng tăng chung của cả 3 quốc gia, nhấn mạnh sự tăng vọt dự kiến của Nhật Bản sau năm 2030.\n3. Đoạn 3 (Body 1): So sánh số liệu của Mỹ và Thụy Điển từ 1940 đến 2040.\n4. Đoạn 4 (Body 2): Phân tích riêng dữ liệu của Nhật Bản (thấp nhất trong quá khứ nhưng bứt phá mạnh trong tương lai).',
                'suggested_outline' => [
                    [
                        'section' => 'Introduction',
                        'hint' => 'Paraphrase đề bài bằng các từ đồng nghĩa: proportion -> percentage, shows -> illustrates/compares, between 1940 and 2040 -> over a period of 100 years.',
                    ],
                    [
                        'section' => 'Overview',
                        'hint' => 'Nêu 2 điểm tổng quan chính: (1) Tỷ lệ người cao tuổi ở cả 3 nước đều có xu hướng tăng; (2) Nhật Bản có mức tăng trưởng ngoạn mục nhất vào cuối kỳ.',
                    ],
                    [
                        'section' => 'Body Paragraph 1: USA & Sweden',
                        'hint' => 'So sánh Mỹ và Thụy Điển: Năm 1940 Mỹ dẫn đầu với gần 10%, sau đó Thụy Điển vượt lên và cả hai cùng đạt khoảng 25% vào năm 2040.',
                    ],
                    [
                        'section' => 'Body Paragraph 2: Japan',
                        'hint' => 'Nhật Bản khởi đầu thấp nhất (5%), duy trì mức thấp cho đến năm 2030 trước khi tăng vọt lên mức cao nhất trong 3 nước (khoảng 27%).',
                    ],
                ],
                'status' => ContentStatus::PUBLISHED,
                'published_at' => now()->subDays(5),
                'order_index' => 1,
            ]
        );

        // Sample Essay Band 5.0 for Prompt 1
        SampleEssay::updateOrCreate(
            ['writing_prompt_id' => $prompt1->id, 'band_score' => 5.0],
            [
                'title' => 'Bài mẫu Band 5.0 (Cơ bản & Nhận xét lỗi thường gặp)',
                'author_type' => 'Bài học viên phân tích',
                'essay_text' => "The line graph shows information about how many elderly people aged 65 and over lived in Japan, Sweden and the USA from 1940 to 2040.

Overall, the percentage of older people in all three nations increases. In addition, Japan is the lowest at first but it becomes the highest at the end.

In 1940, the proportion in the USA was about 9%, while Sweden was about 7% and Japan was only 5%. After that, the USA and Sweden increased slowly. In 1980, the USA reached 15% and Sweden reached 14%. Japan was still around 3% to 4%.

From 2000 to 2040, the numbers continue to go up. By 2040, the proportion of elderly people in the USA and Sweden will be around 23% and 25%. Japan will have a big increase to reach 27%, which is the highest number in the chart.",
                'word_count' => 148,
                'analysis_notes' => "Ưu điểm: Có cấu trúc đoạn văn, có Overview ngắn gọn và nêu được số liệu cơ bản.\nNhược điểm cần khắc phục: Dưới 150 từ (148 từ bị trừ điểm TA). Từ vựng lặp lại đơn giản (increase, lowest, highest, numbers go up). Cấu trúc câu đơn giản, ít mệnh đề quan hệ hay cấu trúc so sánh phức.",
                'highlighted_vocabulary' => [
                    ['word' => 'elderly people', 'meaning' => 'người cao tuổi', 'band' => '5.5', 'note' => 'Paraphrase tốt cho people aged 65 and over'],
                    ['nations', 'meaning' => 'quốc gia', 'band' => '5.5', 'note' => 'Từ thay thế cho countries'],
                ],
                'highlighted_structures' => [
                    ['pattern' => 'while Sweden was about 7%', 'note' => 'Liên từ while tạo câu ghép'],
                ],
                'order_index' => 1,
            ]
        );

        // Sample Essay Band 6.5+ for Prompt 1
        SampleEssay::updateOrCreate(
            ['writing_prompt_id' => $prompt1->id, 'band_score' => 6.5],
            [
                'title' => 'Bài mẫu Band 6.5+ (Học thuật & Cấu trúc so sánh đa dạng)',
                'author_type' => 'Giảng viên Lattice IELTS',
                'essay_text' => "The line chart illustrates the changes in the proportion of elderly citizens aged 65 and above in three distinct countries, namely Japan, Sweden, and the USA, over a century-long period starting from 1940 and projected up to 2040.

Overall, it is evident that all three nations are expected to witness an upward trend in their ageing populations. Notably, while Japan initially had the lowest percentage of senior residents, it is predicted to experience the most dramatic surge and surpass both Sweden and the United States by the end of the timeline.

Looking at the USA and Sweden in detail, approximately 9% of the American population was 65 or older in 1940, compared to roughly 7% in Sweden. Both countries experienced a steady increase over the subsequent decades, with Sweden overtaking the USA in the late 1990s. By 2040, the figures for both nations are forecasted to climb significantly to around 23% and 25% respectively.

In stark contrast, the proportion of elderly people in Japan remained relatively flat at around 3% to 5% between 1940 and 2000. However, the Japanese ageing population has begun to rise steadily and is anticipated to soar exponentially after 2030, ultimately reaching an unprecedented peak of nearly 27% by 2040.",
                'word_count' => 205,
                'analysis_notes' => "Điểm mạnh Task Achievement (Band 7.0): Overview rất rõ ràng và sắc nét, so sánh đầy đủ 3 quốc gia kèm mốc thời gian và số liệu chính xác.\nĐiểm mạnh Coherence & Cohesion (Band 7.0): Sử dụng liên từ chuyển tiếp xuất sắc (Looking at... in detail, In stark contrast, Notably, However).\nĐiểm mạnh Lexical Resource (Band 6.5 - 7.0): Từ vựng học thuật phong phú (elderly citizens, senior residents, dramatic surge, unprecedented peak, soar exponentially).\nĐiểm mạnh Grammatical Range (Band 7.0): Sử dụng thì quá khứ và dự đoán tương lai (is forecasted to, is anticipated to) cùng mệnh đề phân từ và câu phức.",
                'highlighted_vocabulary' => [
                    ['word' => 'elderly citizens / senior residents', 'meaning' => 'công dân cao tuổi', 'band' => '7.0', 'note' => 'Paraphrasing xuất sắc cho aged 65 and over'],
                    ['word' => 'dramatic surge', 'meaning' => 'sự tăng vọt mạnh mẽ', 'band' => '7.0', 'note' => 'Collocation học thuật'],
                    ['word' => 'soar exponentially', 'meaning' => 'tăng vọt theo cấp số nhân', 'band' => '7.5', 'note' => 'Cụm từ ghi điểm LR cao'],
                    ['word' => 'unprecedented peak', 'meaning' => 'đỉnh cao chưa từng có', 'band' => '7.5', 'note' => 'Cụm collocation nâng cao'],
                ],
                'highlighted_structures' => [
                    ['pattern' => 'while Japan initially had..., it is predicted to...', 'note' => 'Cấu trúc nhượng bộ so sánh đối lập'],
                    ['pattern' => 'in stark contrast, ...', 'note' => 'Cụm liên kết đối lập mạnh mẽ'],
                ],
                'order_index' => 2,
            ]
        );

        // 2. Task 2: Opinion / Agree or Disagree (University Education vs Workplace Skills)
        $prompt2 = WritingPrompt::updateOrCreate(
            ['slug' => 'university-education-practical-skills-vs-knowledge'],
            [
                'task_type' => WritingTaskType::TASK_2,
                'prompt_type' => WritingPromptType::DISCUSSION,
                'topic_id' => $topicEdu?->id ?? null,
                'title' => 'University Education: Workplace Skills vs Pure Knowledge',
                'prompt_text' => 'Some people believe that the primary purpose of university education is to prepare graduates for employment by providing workplace skills. Others argue that universities should focus purely on providing access to knowledge regardless of career utility. Discuss both views and give your own opinion.',
                'level' => VocabularyLevel::BAND_4_5_5_0,
                'min_words' => 250,
                'time_limit_minutes' => 40,
                'guidance' => '1. Mở bài (Intro): Paraphrase lại đề bài và nêu rõ quan điểm cá nhân (Thesis Statement).\n2. Thân bài 1 (Body 1): Bàn luận về quan điểm đào tạo kỹ năng thực hành cho công việc (tỷ lệ có việc làm, đóng góp kinh tế).\n3. Thân bài 2 (Body 2): Bàn luận về giá trị của tri thức học thuật thuần túy (nghiên cứu khoa học, tư duy phản biện, phát triển văn hóa).\n4. Kết bài (Conclusion): Khẳng định lại quan điểm rằng trường đại học lý tưởng cần dung hòa cả hai mục tiêu.',
                'suggested_outline' => [
                    [
                        'section' => 'Introduction',
                        'hint' => 'Nêu bối cảnh về vai trò của giáo dục bậc cao và đưa ra lập trường cân bằng giữa kỹ năng thực tiễn và tri thức học thuật.',
                    ],
                    [
                        'section' => 'Body Paragraph 1: Employment Preparation',
                        'hint' => 'Giải thích tại sao đào tạo thực tiễn lại quan trọng: Học viên cần có việc làm sau tốt nghiệp để trang trải học phí và đóng góp cho lực lượng lao động.',
                    ],
                    [
                        'section' => 'Body Paragraph 2: Pure Academic Pursuit',
                        'hint' => 'Giải thích tầm quan trọng của tri thức thuần túy: Thúc đẩy các phát minh khoa học nền tảng, triết học và khả năng tư duy độc lập.',
                    ],
                    [
                        'section' => 'Conclusion',
                        'hint' => 'Tóm tắt lại hai quan điểm và khẳng định một trường đại học toàn diện nên tích hợp cả hai yếu tố.',
                    ],
                ],
                'status' => ContentStatus::PUBLISHED,
                'published_at' => now()->subDays(4),
                'order_index' => 2,
            ]
        );

        // Sample Essay Band 6.5+ for Prompt 2
        SampleEssay::updateOrCreate(
            ['writing_prompt_id' => $prompt2->id, 'band_score' => 6.5],
            [
                'title' => 'Bài mẫu Band 6.5+ (Cân bằng, Lập luận mạch lạc & Từ vựng chủ đề Giáo dục)',
                'author_type' => 'Giảng viên Lattice IELTS',
                'essay_text' => "There is an ongoing debate regarding the fundamental purpose of higher education. While some individuals argue that tertiary institutions should prioritize career-oriented training to enhance graduate employability, others maintain that universities should remain sanctuaries for pure academic pursuit and theoretical knowledge. In my view, both aspects are equally indispensable for a well-rounded educational system.

On the one hand, advocates of vocational-focused curricula emphasize that modern students invest significant time and financial resources into acquiring a degree. Therefore, academic programs ought to equip learners with pragmatic workplace competencies, such as digital literacy, communication, and industry-specific expertise. Without these applicable skills, graduates may face considerable difficulties in securing gainful employment in an increasingly competitive labor market. For example, engineering and medical students must undergo rigorous hands-on training to fulfill professional requirements effectively.

On the other hand, focusing exclusively on job preparation risks undermining the core mission of universities. Higher education has historically fostered critical thinking, philosophical inquiry, and fundamental scientific research, which might not yield immediate commercial profits but are vital for long-term societal progress. Disciplines such as theoretical physics, history, and literature cultivate intellectual curiosity and broaden human understanding. If universities merely function as vocational training centers, profound scientific discoveries and cultural advancements could be severely compromised.

In conclusion, while preparing students for the workforce is essential for economic stability, it should not overshadow the intrinsic value of academic research and general knowledge. In my opinion, the most progressive universities must adopt a synergistic approach that merges theoretical foundations with practical vocational training.",
                'word_count' => 263,
                'analysis_notes' => "Task Response (Band 7.0): Trả lời thỏa đáng cả 2 quan điểm và nêu rõ lập luận cá nhân xuyên suốt từ mở bài đến kết bài.\nCoherence & Cohesion (Band 7.0): Phân đoạn chuẩn 4 đoạn, các câu nối và cấu trúc logic chuyển đoạn tự nhiên.\nLexical Resource (Band 7.0): Sử dụng vốn từ vựng giáo dục phong phú (tertiary institutions, graduate employability, pragmatic workplace competencies, gainful employment, intellectual curiosity, synergistic approach).\nGrammatical Range & Accuracy (Band 6.5 - 7.0): Cấu trúc câu đa dạng, dùng mệnh đề điều kiện, câu bị động và các mệnh đề quan hệ chuẩn xác.",
                'highlighted_vocabulary' => [
                    ['word' => 'tertiary institutions', 'meaning' => 'các cơ sở giáo dục đại học', 'band' => '7.0', 'note' => 'Từ học thuật cao cấp cho universities'],
                    ['word' => 'graduate employability', 'meaning' => 'khả năng có việc làm của sinh viên tốt nghiệp', 'band' => '7.0', 'note' => 'Collocation chuyên ngành Giáo dục & Lao động'],
                    ['word' => 'pragmatic workplace competencies', 'meaning' => 'năng lực làm việc thực tiễn', 'band' => '7.5', 'note' => 'Cụm từ ghi điểm Lexical Resource cao'],
                    ['word' => 'gainful employment', 'meaning' => 'công việc sinh lời / có thu nhập tốt', 'band' => '7.5', 'note' => 'Collocation học thuật đắt giá'],
                    ['word' => 'synergistic approach', 'meaning' => 'phương pháp tiếp cận cộng hưởng / kết hợp', 'band' => '8.0', 'note' => 'Từ vựng C2 nâng cao cho phần kết luận'],
                ],
                'highlighted_structures' => [
                    ['pattern' => 'While some individuals argue that..., others maintain that...', 'note' => 'Mở bài mẫu mực cho dạng bài Discuss both views'],
                    ['pattern' => 'If universities merely function as..., profound discoveries could be...', 'note' => 'Câu điều kiện loại 2 thể hiện tư duy phản biện sắc sảo'],
                ],
                'order_index' => 1,
            ]
        );
    }
}
