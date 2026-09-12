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
        Schema::create('vocabulary_practice_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('vocabulary_practice_sessions')->cascadeOnDelete();
            $table->foreignId('vocabulary_item_id')->constrained('vocabulary_items')->cascadeOnDelete();
            $table->string('question_type'); // multiple_choice, meaning_select, fill_in_blank, matching, contextual
            $table->json('question_data'); // prompt, options, correct_answer, explanation, etc.
            $table->text('user_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->index('session_id');
            $table->index('vocabulary_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabulary_practice_answers');
    }
};
