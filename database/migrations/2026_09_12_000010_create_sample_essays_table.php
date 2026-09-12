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
        Schema::create('sample_essays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('writing_prompt_id')->constrained('writing_prompts')->cascadeOnDelete();
            $table->string('title');
            $table->decimal('band_score', 3, 1)->index(); // e.g. 5.0, 6.5, 8.0
            $table->string('author_type')->default('Lattice IELTS');
            $table->longText('essay_text');
            $table->unsignedSmallInteger('word_count')->default(0);
            $table->text('analysis_notes')->nullable();
            $table->json('highlighted_vocabulary')->nullable();
            $table->json('highlighted_structures')->nullable();
            $table->string('status')->default('published');
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sample_essays');
    }
};
