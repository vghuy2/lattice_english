<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\VocabularyTopic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VocabularyTopic>
 */
class VocabularyTopicFactory extends Factory
{
    protected $model = VocabularyTopic::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(2, true);

        return [
            'title' => ucfirst($title),
            'slug' => Str::slug($title),
            'description' => fake()->sentence(),
            'icon' => '📚',
            'sort_order' => fake()->numberBetween(1, 20),
            'status' => ContentStatus::PUBLISHED,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => ContentStatus::DRAFT,
        ]);
    }
}
