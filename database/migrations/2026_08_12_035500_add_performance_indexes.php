<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('careers', function (Blueprint $table) {
            $table->index(
                'is_active',
                'careers_is_active_index',
            );
        });

        Schema::table(
            'skill_prerequisites',
            function (Blueprint $table) {
                $table->index(
                    'prerequisite_skill_id',
                    'skill_prerequisites_prerequisite_index',
                );
            },
        );

        Schema::table(
            'assessments',
            function (Blueprint $table) {
                $table->index(
                    [
                        'career_id',
                        'is_active',
                    ],
                    'assessments_career_active_index',
                );
            },
        );

        Schema::table(
            'assessment_results',
            function (Blueprint $table) {
                $table->index(
                    [
                        'user_id',
                        'created_at',
                    ],
                    'assessment_results_user_created_index',
                );
            },
        );

        Schema::table(
            'roadmaps',
            function (Blueprint $table) {
                $table->index(
                    [
                        'user_id',
                        'is_active',
                    ],
                    'roadmaps_user_active_index',
                );
            },
        );

        Schema::table(
            'progress_logs',
            function (Blueprint $table) {
                $table->index(
                    [
                        'user_id',
                        'logged_at',
                    ],
                    'progress_logs_user_logged_at_index',
                );
            },
        );

        Schema::table(
            'evaluations',
            function (Blueprint $table) {
                $table->index(
                    [
                        'user_id',
                        'created_at',
                    ],
                    'evaluations_user_created_index',
                );
            },
        );

        Schema::table(
            'user_projects',
            function (Blueprint $table) {
                $table->index(
                    [
                        'user_id',
                        'status',
                        'updated_at',
                    ],
                    'user_projects_user_status_updated_index',
                );
            },
        );
    }

    public function down(): void
    {
        Schema::table('careers', function (Blueprint $table) {
            $table->dropIndex(
                'careers_is_active_index',
            );
        });

        Schema::table(
            'skill_prerequisites',
            function (Blueprint $table) {
                $table->dropIndex(
                    'skill_prerequisites_prerequisite_index',
                );
            },
        );

        Schema::table(
            'assessments',
            function (Blueprint $table) {
                $table->dropIndex(
                    'assessments_career_active_index',
                );
            },
        );

        Schema::table(
            'assessment_results',
            function (Blueprint $table) {
                $table->dropIndex(
                    'assessment_results_user_created_index',
                );
            },
        );

        Schema::table(
            'roadmaps',
            function (Blueprint $table) {
                $table->dropIndex(
                    'roadmaps_user_active_index',
                );
            },
        );

        Schema::table(
            'progress_logs',
            function (Blueprint $table) {
                $table->dropIndex(
                    'progress_logs_user_logged_at_index',
                );
            },
        );

        Schema::table(
            'evaluations',
            function (Blueprint $table) {
                $table->dropIndex(
                    'evaluations_user_created_index',
                );
            },
        );

        Schema::table(
            'user_projects',
            function (Blueprint $table) {
                $table->dropIndex(
                    'user_projects_user_status_updated_index',
                );
            },
        );
    }
};
