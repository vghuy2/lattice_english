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
        Schema::create('writing_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('writing_prompt_id')->constrained('writing_prompts')->cascadeOnDelete();
            $table->longText('essay_content')->nullable();
            $table->unsignedSmallInteger('word_count')->default(0);
            $table->unsignedInteger('time_spent_seconds')->default(0);
            $table->string('status')->default('draft')->index(); // draft, submitted, grading, graded
            $table->decimal('overall_score', 3, 1)->nullable()->index(); // e.g. 5.5, 6.0
            $table->decimal('ta_score', 3, 1)->nullable(); // Task Achievement / Task Response
            $table->decimal('cc_score', 3, 1)->nullable(); // Coherence & Cohesion
            $table->decimal('lr_score', 3, 1)->nullable(); // Lexical Resource
            $table->decimal('gra_score', 3, 1)->nullable(); // Grammatical Range & Accuracy
            $table->json('scoring_breakdown')->nullable();
            $table->text('feedback_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('writing_submissions');
    }
};
