<?php

namespace Database\Factories;

use App\Enums\IeltsType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::STUDENT,
            'status' => UserStatus::ACTIVE,
            'current_band' => 4.5,
            'target_band' => 6.5,
            'test_type' => IeltsType::ACADEMIC,
            'target_date' => now()->addMonths(3),
            'study_days_per_week' => 5,
            'study_goal' => 'Mục tiêu cải thiện Lexical Resource và Task Response band 6.5+',
            'onboarding_completed_at' => now(),
        ];
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::STUDENT,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::ADMIN,
            'onboarding_completed_at' => now(),
        ]);
    }

    public function notOnboarded(): static
    {
        return $this->state(fn (array $attributes) => [
            'onboarding_completed_at' => null,
            'current_band' => null,
            'target_band' => null,
        ]);
    }

    public function banned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatus::BANNED,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
