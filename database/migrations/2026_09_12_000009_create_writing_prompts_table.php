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
        Schema::create('writing_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('task_type')->index(); // 'task_1', 'task_2'
            $table->string('prompt_type')->index(); // 'line_graph', 'opinion', etc.
            $table->foreignId('topic_id')->nullable()->constrained('vocabulary_topics')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('prompt_text');
            $table->string('image_path')->nullable();
            $table->string('level')->default('band_4_5_5_0');
            $table->unsignedSmallInteger('min_words')->default(150);
            $table->unsignedSmallInteger('time_limit_minutes')->default(20);
            $table->text('guidance')->nullable();
            $table->json('suggested_outline')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('writing_prompts');
    }
};
