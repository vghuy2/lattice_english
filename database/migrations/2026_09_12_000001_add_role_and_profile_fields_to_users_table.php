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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->after('email');
            $table->string('status')->default('active')->after('role');
            $table->string('avatar')->nullable()->after('status');
            $table->decimal('current_band', 3, 1)->nullable()->after('avatar');
            $table->decimal('target_band', 3, 1)->nullable()->after('current_band');
            $table->string('test_type')->default('academic')->after('target_band');
            $table->date('target_date')->nullable()->after('test_type');
            $table->unsignedTinyInteger('study_days_per_week')->default(5)->after('target_date');
            $table->text('study_goal')->nullable()->after('study_days_per_week');
            $table->timestamp('onboarding_completed_at')->nullable()->after('study_goal');
            
            $table->index('role');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'role',
                'status',
                'avatar',
                'current_band',
                'target_band',
                'test_type',
                'target_date',
                'study_days_per_week',
                'study_goal',
                'onboarding_completed_at',
            ]);
        });
    }
};
