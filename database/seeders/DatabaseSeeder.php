<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed User Accounts (admin@ielts.vn, student@ielts.vn, etc.)
        $this->call(UserSeeder::class);

        // 2. Seed Vocabulary Topics, Lessons, and Items
        $this->call(VocabularySeeder::class);

        // 3. Seed Writing Prompts, Sample Essays, Rubrics and Rules
        $this->call(WritingSeeder::class);

        // 4. Seed Demo Student Activity, Reviews and Submissions
        $this->call(DemoSeeder::class);
    }
}
