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
        Schema::table('writing_submissions', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'ws_user_status_idx');
            $table->index(['user_id', 'writing_prompt_id', 'status'], 'ws_user_prompt_status_idx');
        });

        Schema::table('student_vocabulary_reviews', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'svr_user_status_idx');
            $table->index(['user_id', 'next_review_at'], 'svr_user_next_review_idx');
            $table->index(['user_id', 'is_favorite'], 'svr_user_favorite_idx');
        });

        Schema::table('vocabulary_practice_sessions', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'vps_user_status_idx');
            $table->index(['user_id', 'session_type', 'status'], 'vps_user_type_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('writing_submissions', function (Blueprint $table) {
            $table->dropIndex('ws_user_status_idx');
            $table->dropIndex('ws_user_prompt_status_idx');
        });

        Schema::table('student_vocabulary_reviews', function (Blueprint $table) {
            $table->dropIndex('svr_user_status_idx');
            $table->dropIndex('svr_user_next_review_idx');
            $table->dropIndex('svr_user_favorite_idx');
        });

        Schema::table('vocabulary_practice_sessions', function (Blueprint $table) {
            $table->dropIndex('vps_user_status_idx');
            $table->dropIndex('vps_user_type_status_idx');
        });
    }
};
