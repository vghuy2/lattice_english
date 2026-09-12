<?php

namespace Database\Factories;

use App\Enums\PartOfSpeech;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VocabularyItem>
 */
class VocabularyItemFactory extends Factory
{
    protected $model = VocabularyItem::class;

    public function definition(): array
    {
        $word = fake()->unique()->word();

        return [
            'lesson_id' => VocabularyLesson::factory(),
            'word' => $word,
            'vietnamese_meaning' => 'Nghĩa tiếng Việt của ' . $word,
            'part_of_speech' => PartOfSpeech::NOUN,
            'ipa' => '/' . $word . '/',
            'example_sentence' => 'This is an example sentence demonstrating the word ' . $word . ' in an IELTS essay.',
            'example_sentence_vi' => 'Đây là câu ví dụ minh họa cách dùng trong bài luận IELTS.',
            'collocations' => ['major ' . $word, 'develop ' . $word],
            'synonyms' => ['concept', 'idea'],
            'antonyms' => [],
            'writing_notes' => 'Lưu ý dùng đúng ngữ cảnh trong Writing Task 2.',
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
