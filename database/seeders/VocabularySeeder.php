<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\PartOfSpeech;
use App\Enums\VocabularyLevel;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use Illuminate\Database\Seeder;

class VocabularySeeder extends Seeder
{
    public function run(): void
    {
        // --------------------------------------------------------------------
        // TOPIC 1: Education & Academic Life
        // --------------------------------------------------------------------
        $eduTopic = VocabularyTopic::updateOrCreate(
            ['slug' => 'education-academic-life'],
            [
                'title' => 'Education & Academic Life',
                'description' => 'Chủ đề Giáo dục, trường đại học, phương pháp giảng dạy và kỹ năng học tập thiết yếu trong IELTS.',
                'icon' => '🎓',
                'sort_order' => 1,
                'status' => ContentStatus::PUBLISHED,
            ]
        );

        $eduLesson1 = VocabularyLesson::updateOrCreate(
            ['slug' => 'higher-education-and-vocational-skills'],
            [
                'topic_id' => $eduTopic->id,
                'title' => 'Higher Education & Vocational Skills',
                'description' => 'Từ vựng trọng tâm về giáo dục đại học, học nghề và chương trình đào tạo chuẩn bị cho bài viết Task 2.',
                'level' => VocabularyLevel::BAND_4_5_5_0,
                'estimated_minutes' => 15,
                'sort_order' => 1,
                'status' => ContentStatus::PUBLISHED,
                'published_at' => now()->subDays(2),
            ]
        );

        $eduLesson2 = VocabularyLesson::updateOrCreate(
            ['slug' => 'digital-learning-and-classrooms'],
            [
                'topic_id' => $eduTopic->id,
                'title' => 'Digital Learning & Modern Classrooms',
                'description' => 'Từ vựng và collocations về học trực tuyến (E-learning), đào tạo từ xa và công nghệ lớp học.',
                'level' => VocabularyLevel::BAND_4_5_5_0,
                'estimated_minutes' => 15,
                'sort_order' => 2,
                'status' => ContentStatus::PUBLISHED,
                'published_at' => now()->subDays(2),
            ]
        );

        $eduItems1 = [
            [
                'word' => 'curriculum',
                'vietnamese_meaning' => 'chương trình giảng dạy, khung đào tạo',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/kəˈrɪk.jə.ləm/',
                'example_sentence' => 'The national curriculum should incorporate practical vocational skills alongside theoretical knowledge.',
                'example_sentence_vi' => 'Khung chương trình quốc gia nên kết hợp các kỹ năng nghề thực tế bên cạnh kiến thức lý thuyết.',
                'collocations' => ['school curriculum', 'core curriculum', 'reform the curriculum'],
                'synonyms' => ['syllabus', 'course of study', 'program'],
                'antonyms' => ['extracurricular activities'],
                'writing_notes' => 'Rất hữu ích khi viết mở bài và thân bài đề Task 2 về việc trường học nên dạy kiến thức học thuật hay kỹ năng mềm.',
                'sort_order' => 1,
            ],
            [
                'word' => 'tuition fees',
                'vietnamese_meaning' => 'học phí',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/tjuːˈɪʃ.ən fiːz/',
                'example_sentence' => 'Exorbitant tuition fees discourage students from low-income families from pursuing tertiary education.',
                'example_sentence_vi' => 'Học phí đắt đỏ khiến nhiều sinh viên từ các gia đình thu nhập thấp nản lòng khi theo học đại học.',
                'collocations' => ['pay tuition fees', 'waive tuition fees', 'high tuition fees'],
                'synonyms' => ['education costs', 'university charges'],
                'antonyms' => ['free education', 'scholarship'],
                'writing_notes' => 'Cụm từ chủ lực trong đề tài: "Should university education be free for all students?".',
                'sort_order' => 2,
            ],
            [
                'word' => 'vocational training',
                'vietnamese_meaning' => 'đào tạo nghề, học nghề',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/voʊˈkeɪ.ʃən.əl ˈtreɪ.nɪŋ/',
                'example_sentence' => 'Vocational training prepares students directly for specific occupations such as mechanics and carpentry.',
                'example_sentence_vi' => 'Đào tạo nghề chuẩn bị trực tiếp cho học sinh các công việc cụ thể như cơ khí và thợ mộc.',
                'collocations' => ['provide vocational training', 'vocational course', 'vocational school'],
                'synonyms' => ['practical education', 'job training', 'career education'],
                'antonyms' => ['academic education'],
                'writing_notes' => 'Dùng khi đối sánh giữa học đại học (academic degree) và học nghề (vocational training).',
                'sort_order' => 3,
            ],
            [
                'word' => 'compulsory subject',
                'vietnamese_meaning' => 'môn học bắt buộc',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/kəmˈpʌl.sər.i ˈsʌb.dʒɪkt/',
                'example_sentence' => 'Some educators argue that physical education and music ought to be compulsory subjects.',
                'example_sentence_vi' => 'Một số nhà giáo dục cho rằng thể dục và âm nhạc nên là những môn học bắt buộc.',
                'collocations' => ['make a compulsory subject', 'compulsory curriculum'],
                'synonyms' => ['mandatory subject', 'required course'],
                'antonyms' => ['elective subject', 'optional course'],
                'writing_notes' => 'Dùng trong đề bài bàn luận về môn học bắt buộc (Art, PE, History) ở bậc phổ thông.',
                'sort_order' => 4,
            ],
        ];

        foreach ($eduItems1 as $itemData) {
            VocabularyItem::updateOrCreate(
                ['lesson_id' => $eduLesson1->id, 'word' => $itemData['word']],
                $itemData
            );
        }

        $eduItems2 = [
            [
                'word' => 'distance learning',
                'vietnamese_meaning' => 'học từ xa, học trực tuyến',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/ˈdɪs.təns ˈlɜː.nɪŋ/',
                'example_sentence' => 'Distance learning allows students living in remote areas to acquire quality qualifications.',
                'example_sentence_vi' => 'Học từ xa cho phép học sinh sống ở vùng sâu vùng xa đạt được các văn bằng chất lượng.',
                'collocations' => ['adopt distance learning', 'online degree program'],
                'synonyms' => ['e-learning', 'remote studying'],
                'antonyms' => ['traditional classroom'],
                'writing_notes' => 'Chủ điểm thường gặp khi viết so sánh ưu/nhược điểm của học online và học offline.',
                'sort_order' => 1,
            ],
            [
                'word' => 'self-discipline',
                'vietnamese_meaning' => 'tính tự giác, kỷ luật bản thân',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/ˌselfˈdɪs.ə.plɪn/',
                'example_sentence' => 'Online education demands a high level of self-discipline to complete assignments on time.',
                'example_sentence_vi' => 'Học trực tuyến đòi hỏi tính tự giác cao để hoàn thành bài tập đúng hạn.',
                'collocations' => ['require self-discipline', 'develop self-discipline'],
                'synonyms' => ['self-control', 'willpower'],
                'antonyms' => ['procrastination', 'carelessness'],
                'writing_notes' => 'Luận điểm giải thích tại sao nhiều học viên bỏ dở khóa học online.',
                'sort_order' => 2,
            ],
        ];

        foreach ($eduItems2 as $itemData) {
            VocabularyItem::updateOrCreate(
                ['lesson_id' => $eduLesson2->id, 'word' => $itemData['word']],
                $itemData
            );
        }

        // --------------------------------------------------------------------
        // TOPIC 2: Environment & Climate Change
        // --------------------------------------------------------------------
        $envTopic = VocabularyTopic::updateOrCreate(
            ['slug' => 'environment-climate-change'],
            [
                'title' => 'Environment & Climate Change',
                'description' => 'Chủ đề Môi trường, biến đổi khí hậu, năng lượng tái tạo và bảo tồn thiên nhiên.',
                'icon' => '🌱',
                'sort_order' => 2,
                'status' => ContentStatus::PUBLISHED,
            ]
        );

        $envLesson1 = VocabularyLesson::updateOrCreate(
            ['slug' => 'pollution-and-global-warming'],
            [
                'topic_id' => $envTopic->id,
                'title' => 'Pollution & Global Warming',
                'description' => 'Từ vựng miêu tả hiện tượng nóng lên toàn cầu, khí thải nhà kính và suy thoái môi trường.',
                'level' => VocabularyLevel::BAND_4_5_5_0,
                'estimated_minutes' => 15,
                'sort_order' => 1,
                'status' => ContentStatus::PUBLISHED,
                'published_at' => now()->subDays(2),
            ]
        );

        $envItems1 = [
            [
                'word' => 'greenhouse gas emissions',
                'vietnamese_meaning' => 'khí thải nhà kính',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/ˈɡriːn.haʊs ɡæs ɪˈmɪʃ.ənz/',
                'example_sentence' => 'Governments must enforce strict regulations to curb greenhouse gas emissions from heavy industries.',
                'example_sentence_vi' => 'Các chính phủ phải thực thi quy định nghiêm ngặt để hạn chế khí thải nhà kính từ các ngành công nghiệp nặng.',
                'collocations' => ['reduce greenhouse gas emissions', 'cut carbon emissions', 'toxic emissions'],
                'synonyms' => ['carbon footprint', 'exhaust fumes'],
                'antonyms' => ['clean energy output'],
                'writing_notes' => 'Cụm từ ghi điểm cao cho tiêu chí Lexical Resource trong các bài Writing môi trường.',
                'sort_order' => 1,
            ],
            [
                'word' => 'deforestation',
                'vietnamese_meaning' => 'nạn chặt phá rừng, tàn phá rừng',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/diːˌfɒr.ɪˈsteɪ.ʃən/',
                'example_sentence' => 'Widespread deforestation contributes significantly to the loss of biodiversity and climate warming.',
                'example_sentence_vi' => 'Nạn phá rừng trên diện rộng góp phần đáng kể vào sự suy giảm đa dạng sinh học và ấm lên toàn cầu.',
                'collocations' => ['illegal deforestation', 'combat deforestation'],
                'synonyms' => ['logging', 'forest clearance'],
                'antonyms' => ['afforestation', 'reforestation'],
                'writing_notes' => 'Nguyên nhân chính trong các đề về biến đổi khí hậu và tuyệt chủng loài động vật.',
                'sort_order' => 2,
            ],
            [
                'word' => 'renewable energy',
                'vietnamese_meaning' => 'năng lượng tái tạo (mặt trời, gió, nước)',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/rɪˈnjuː.ə.bəl ˈen.ə.dʒi/',
                'example_sentence' => 'Transitioning to renewable energy sources like wind and solar power is crucial for a sustainable future.',
                'example_sentence_vi' => 'Chuyển đổi sang các nguồn năng lượng tái tạo như gió và mặt trời là điều cốt lõi cho một tương lai bền vững.',
                'collocations' => ['invest in renewable energy', 'renewable energy sources', 'clean energy'],
                'synonyms' => ['sustainable energy', 'green power'],
                'antonyms' => ['fossil fuels', 'non-renewable energy'],
                'writing_notes' => 'Giải pháp hàng đầu trong đoạn Thân bài 2 khi viết bài Problem - Solution về ô nhiễm năng lượng.',
                'sort_order' => 3,
            ],
        ];

        foreach ($envItems1 as $itemData) {
            VocabularyItem::updateOrCreate(
                ['lesson_id' => $envLesson1->id, 'word' => $itemData['word']],
                $itemData
            );
        }

        // --------------------------------------------------------------------
        // TOPIC 3: Technology & Artificial Intelligence
        // --------------------------------------------------------------------
        $techTopic = VocabularyTopic::updateOrCreate(
            ['slug' => 'technology-ai-society'],
            [
                'title' => 'Technology & AI in Society',
                'description' => 'Chủ đề Công nghệ, tự động hóa, trí tuệ nhân tạo và ảnh hưởng của Internet tới đời sống con người.',
                'icon' => '💻',
                'sort_order' => 3,
                'status' => ContentStatus::PUBLISHED,
            ]
        );

        $techLesson1 = VocabularyLesson::updateOrCreate(
            ['slug' => 'automation-and-artificial-intelligence'],
            [
                'topic_id' => $techTopic->id,
                'title' => 'Automation & Artificial Intelligence',
                'description' => 'Từ vựng miêu tả sự phát triển của robot, trí tuệ nhân tạo và thay đổi thị trường lao động.',
                'level' => VocabularyLevel::BAND_4_5_5_0,
                'estimated_minutes' => 15,
                'sort_order' => 1,
                'status' => ContentStatus::PUBLISHED,
                'published_at' => now()->subDays(2),
            ]
        );

        $techItems1 = [
            [
                'word' => 'automation',
                'vietnamese_meaning' => 'tự động hóa',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/ˌɔː.təˈmeɪ.ʃən/',
                'example_sentence' => 'Increasing automation in manufacturing has resulted in significant job losses for manual laborers.',
                'example_sentence_vi' => 'Sự gia tăng tự động hóa trong sản xuất đã dẫn tới tình trạng mất việc làm đáng kể cho lao động chân tay.',
                'collocations' => ['factory automation', 'workplace automation', 'embrace automation'],
                'synonyms' => ['mechanisation', 'robotics implementation'],
                'antonyms' => ['manual labor'],
                'writing_notes' => 'Dùng trong các bài viết thảo luận về tác động của công nghệ tới thị trường việc làm.',
                'sort_order' => 1,
            ],
            [
                'word' => 'indispensable',
                'vietnamese_meaning' => 'không thể thiếu, thiết yếu',
                'part_of_speech' => PartOfSpeech::ADJECTIVE,
                'ipa' => '/ˌɪn.dɪˈspen.sə.bəl/',
                'example_sentence' => 'Smartphones and computers have become indispensable tools in contemporary communication.',
                'example_sentence_vi' => 'Điện thoại thông minh và máy tính đã trở thành những công cụ không thể thiếu trong giao tiếp đương đại.',
                'collocations' => ['indispensable tool', 'play an indispensable role'],
                'synonyms' => ['essential', 'vital', 'crucial'],
                'antonyms' => ['dispensable', 'unnecessary'],
                'writing_notes' => 'Tính từ học thuật cực kỳ đắt giá để thay thế cho "very important" trong bài Writing.',
                'sort_order' => 2,
            ],
        ];

        foreach ($techItems1 as $itemData) {
            VocabularyItem::updateOrCreate(
                ['lesson_id' => $techLesson1->id, 'word' => $itemData['word']],
                $itemData
            );
        }

        // --------------------------------------------------------------------
        // TOPIC 4: Health, Nutrition & Modern Lifestyle
        // --------------------------------------------------------------------
        $healthTopic = VocabularyTopic::updateOrCreate(
            ['slug' => 'health-nutrition-lifestyle'],
            [
                'title' => 'Health & Modern Lifestyle',
                'description' => 'Chủ đề Sức khỏe cộng đồng, chế độ dinh dưỡng, lối sống ít vận động và thói quen sinh hoạt.',
                'icon' => '🥗',
                'sort_order' => 4,
                'status' => ContentStatus::PUBLISHED,
            ]
        );

        $healthLesson1 = VocabularyLesson::updateOrCreate(
            ['slug' => 'diet-obesity-and-sedentary-lifestyle'],
            [
                'topic_id' => $healthTopic->id,
                'title' => 'Diet, Obesity & Sedentary Lifestyle',
                'description' => 'Từ vựng về bệnh béo phì, thực phẩm ăn nhanh và lối sống thụ động ở giới trẻ hiện đại.',
                'level' => VocabularyLevel::BAND_4_5_5_0,
                'estimated_minutes' => 15,
                'sort_order' => 1,
                'status' => ContentStatus::PUBLISHED,
                'published_at' => now()->subDays(2),
            ]
        );

        $healthItems1 = [
            [
                'word' => 'sedentary lifestyle',
                'vietnamese_meaning' => 'lối sống thụ động, ít vận động (ngồi nhiều)',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/ˈsed.ən.tər.i ˈlaɪf.staɪl/',
                'example_sentence' => 'A sedentary lifestyle combined with poor dietary choices leads to serious chronic illnesses.',
                'example_sentence_vi' => 'Lối sống ít vận động kết hợp với chế độ ăn uống kém dẫn tới các bệnh mãn tính nghiêm trọng.',
                'collocations' => ['lead a sedentary lifestyle', 'sedentary habits'],
                'synonyms' => ['inactive lifestyle', 'couch potato behavior'],
                'antonyms' => ['active lifestyle'],
                'writing_notes' => 'Cụm collocation chuẩn mực cho mọi bài viết liên quan đến sức khỏe học đường và văn phòng.',
                'sort_order' => 1,
            ],
            [
                'word' => 'obesity rate',
                'vietnamese_meaning' => 'tỷ lệ béo phì',
                'part_of_speech' => PartOfSpeech::NOUN,
                'ipa' => '/oʊˈbiː.sə.ti reɪt/',
                'example_sentence' => 'The alarming increase in childhood obesity rates requires prompt intervention from public health authorities.',
                'example_sentence_vi' => 'Sự gia tăng đáng báo động về tỷ lệ béo phì ở trẻ em đòi hỏi sự can thiệp kịp thời từ cơ quan y tế công cộng.',
                'collocations' => ['childhood obesity', 'tackle obesity'],
                'synonyms' => ['overweight prevalence', 'excess body weight'],
                'antonyms' => ['healthy weight balance'],
                'writing_notes' => 'Dùng trong mở bài và câu chủ đề của các bài viết về thức ăn nhanh và quảng cáo đồ ngọt.',
                'sort_order' => 2,
            ],
        ];

        foreach ($healthItems1 as $itemData) {
            VocabularyItem::updateOrCreate(
                ['lesson_id' => $healthLesson1->id, 'word' => $itemData['word']],
                $itemData
            );
        }
    }
}
