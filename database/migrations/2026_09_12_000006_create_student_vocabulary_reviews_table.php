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
        Schema::create('student_vocabulary_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vocabulary_item_id')->constrained('vocabulary_items')->cascadeOnDelete();
            $table->string('status')->default('learning'); // learning, known, review_needed
            $table->unsignedTinyInteger('mastery_level')->default(1); // 1..5
            $table->boolean('is_favorite')->default(false);
            $table->text('personal_note')->nullable();
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('incorrect_count')->default(0);
            $table->timestamp('last_reviewed_at')->nullable();
            $table->date('next_review_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'vocabulary_item_id']);
            $table->index('user_id');
            $table->index('status');
            $table->index('is_favorite');
            $table->index('next_review_at');
            $table->index('mastery_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_vocabulary_reviews');
    }
};
