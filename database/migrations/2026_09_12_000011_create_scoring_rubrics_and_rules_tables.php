<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scoring_rubrics', function (Blueprint $table) {
            $table->id();
            $table->string('task_type')->index(); // 'task_1', 'task_2'
            $table->string('criterion')->index(); // 'task_achievement_response', 'coherence_cohesion', etc.
            $table->decimal('band_score', 3, 1)->index(); // 4.0, 5.0, 6.0, 7.0, 8.0, 9.0
            $table->text('description');
            $table->json('key_indicators')->nullable();
            $table->timestamps();

            $table->unique(['task_type', 'criterion', 'band_score'], 'rubric_unique_band');
        });

        Schema::create('scoring_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('task_type')->default('all'); // 'all', 'task_1', 'task_2'
            $table->string('criterion')->nullable(); // 'task_achievement_response', 'coherence_cohesion', etc.
            $table->json('parameters')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scoring_rules');
        Schema::dropIfExists('scoring_rubrics');
    }
};
