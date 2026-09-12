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
        Schema::create('vocabulary_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('vocabulary_topics')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('level')->default('band_4_5_5_0');
            $table->string('thumbnail')->nullable();
            $table->unsignedSmallInteger('estimated_minutes')->default(15);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('topic_id');
            $table->index('level');
            $table->index('status');
            $table->index('published_at');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabulary_lessons');
    }
};
