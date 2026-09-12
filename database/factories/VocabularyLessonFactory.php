<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VocabularyLesson>
 */
class VocabularyLessonFactory extends Factory
{
    protected $model = VocabularyLesson::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'topic_id' => VocabularyTopic::factory(),
            'title' => ucfirst($title),
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'level' => VocabularyLevel::BAND_4_5_5_0,
            'estimated_minutes' => 15,
            'sort_order' => fake()->numberBetween(1, 10),
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => ContentStatus::DRAFT,
            'published_at' => null,
        ]);
    }

    public function scheduledForFuture(): static
    {
        return $this->state(fn () => [
            'status' => ContentStatus::PUBLISHED,
            'published_at' => now()->addDays(7),
        ]);
    }
}
