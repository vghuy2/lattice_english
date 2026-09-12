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
        Schema::create('vocabulary_practice_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained('vocabulary_lessons')->cascadeOnDelete();
            $table->string('session_type')->default('lesson_practice'); // lesson_practice, review_quiz
            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('correct_answers')->default(0);
            $table->decimal('accuracy_rate', 5, 2)->default(0.00);
            $table->string('status')->default('in_progress'); // in_progress, completed
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('lesson_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabulary_practice_sessions');
    }
};
