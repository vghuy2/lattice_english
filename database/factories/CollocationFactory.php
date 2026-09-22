<?php

namespace Database\Factories;

use App\Enums\CollocationType;
use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Models\Collocation;
use App\Models\VocabularyTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Collocation>
 */
class CollocationFactory extends Factory
{
    protected $model = Collocation::class;

    public function definition(): array
    {
        return [
            'topic_id' => VocabularyTopic::factory(),
            'phrase' => fake()->unique()->words(3, true),
            'meaning' => fake()->sentence(),
            'type' => fake()->randomElement(CollocationType::cases()),
            'level' => fake()->randomElement(VocabularyLevel::cases()),
            'example_sentence' => fake()->sentence(),
            'example_sentence_vi' => fake()->sentence(),
            'writing_notes' => fake()->optional()->sentence(),
            'status' => ContentStatus::PUBLISHED,
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => ContentStatus::DRAFT,
        ]);
    }
}
