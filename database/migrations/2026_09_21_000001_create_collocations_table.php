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
        Schema::create('collocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->nullable()->constrained('vocabulary_topics')->nullOnDelete();
            $table->string('phrase');
            $table->string('meaning');
            $table->string('type')->default('verb_noun');
            $table->string('level')->default('band_4_5_5_0');
            $table->text('example_sentence')->nullable();
            $table->text('example_sentence_vi')->nullable();
            $table->text('writing_notes')->nullable();
            $table->string('status')->default('published');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('topic_id');
            $table->index('phrase');
            $table->index('type');
            $table->index('status');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collocations');
    }
};
