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
        Schema::create('vocabulary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('vocabulary_lessons')->cascadeOnDelete();
            $table->string('word');
            $table->string('vietnamese_meaning');
            $table->string('part_of_speech')->default('noun');
            $table->string('ipa')->nullable();
            $table->string('audio')->nullable();
            $table->text('example_sentence');
            $table->text('example_sentence_vi')->nullable();
            $table->json('collocations')->nullable();
            $table->json('synonyms')->nullable();
            $table->json('antonyms')->nullable();
            $table->text('writing_notes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('lesson_id');
            $table->index('word');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabulary_items');
    }
};
