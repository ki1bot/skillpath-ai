<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->index()->after('password');
            $table->string('study_program')->nullable()->after('role');
            $table->unsignedTinyInteger('semester')->nullable()->after('study_program');
            $table->string('interest_area')->nullable()->after('semester');
            $table->text('experience')->nullable()->after('interest_area');
            $table->unsignedTinyInteger('weekly_study_hours')->default(6)->after('experience');
            $table->foreignId('target_career_id')->nullable()->after('weekly_study_hours')->constrained('careers')->nullOnDelete();
            $table->timestamp('onboarding_completed_at')->nullable()->after('target_career_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('target_career_id');
            $table->dropColumn([
                'role',
                'study_program',
                'semester',
                'interest_area',
                'experience',
                'weekly_study_hours',
                'onboarding_completed_at',
            ]);
        });
    }
};
