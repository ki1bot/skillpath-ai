<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('assessment_results', 'self_rating')) {
            return;
        }

        DB::table('assessment_results')->update([
            'score' => DB::raw(
                'CASE WHEN is_correct THEN 100 ELSE 0 END',
            ),
        ]);

        $assessmentUserSkills = DB::table('user_skills')
            ->where(
                'source',
                'assessment',
            )
            ->get([
                'id',
                'user_id',
                'skill_id',
            ]);

        foreach ($assessmentUserSkills as $userSkill) {
            $latestAttemptUuid = DB::table('assessment_results')
                ->where(
                    'user_id',
                    (int) $userSkill->user_id,
                )
                ->where(
                    'skill_id',
                    (int) $userSkill->skill_id,
                )
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->value('attempt_uuid');

            if (
                ! is_string($latestAttemptUuid)
                || $latestAttemptUuid === ''
            ) {
                continue;
            }

            $latestSkillResults = DB::table('assessment_results')
                ->where(
                    'user_id',
                    (int) $userSkill->user_id,
                )
                ->where(
                    'skill_id',
                    (int) $userSkill->skill_id,
                )
                ->where(
                    'attempt_uuid',
                    $latestAttemptUuid,
                );

            $averageScore = $latestSkillResults
                ->avg('score');

            if (! is_numeric($averageScore)) {
                continue;
            }

            $lastAssessedAt = $latestSkillResults
                ->max('created_at');

            DB::table('user_skills')
                ->where(
                    'id',
                    (int) $userSkill->id,
                )
                ->update([
                    'score' => round(
                        (float) $averageScore,
                        2,
                    ),
                    'last_assessed_at' => $lastAssessedAt,
                    'updated_at' => now(),
                ]);
        }

        Schema::table(
            'assessment_results',
            function (Blueprint $table) {
                $table->dropColumn('self_rating');
            },
        );
    }

    public function down(): void
    {
        if (Schema::hasColumn('assessment_results', 'self_rating')) {
            return;
        }

        Schema::table(
            'assessment_results',
            function (Blueprint $table) {
                $table
                    ->unsignedTinyInteger('self_rating')
                    ->default(0);
            },
        );
    }
};
