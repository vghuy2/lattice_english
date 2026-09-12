<?php

namespace Database\Seeders;

use App\Enums\IeltsType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tài khoản Quản trị viên (Admin)
        User::updateOrCreate(
            ['email' => 'admin@ielts.vn'],
            [
                'name' => 'Quản Trị Viên IELTS',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
                'onboarding_completed_at' => now(),
            ]
        );

        // 2. Tài khoản Học viên (Student)
        User::updateOrCreate(
            ['email' => 'student@ielts.vn'],
            [
                'name' => 'Học Viên IELTS',
                'password' => Hash::make('password'),
                'role' => UserRole::STUDENT,
                'status' => UserStatus::ACTIVE,
                'current_band' => 4.5,
                'target_band' => 6.5,
                'test_type' => IeltsType::ACADEMIC,
                'target_date' => now()->addMonths(3),
                'study_days_per_week' => 5,
                'study_goal' => 'Mục tiêu bứt phá từ vựng và luyện Writing đạt Band 6.5+',
                'email_verified_at' => now(),
                'onboarding_completed_at' => now(),
            ]
        );
    }
}
