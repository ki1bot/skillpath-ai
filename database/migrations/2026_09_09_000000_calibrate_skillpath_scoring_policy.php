<?php

use App\Support\SkillPathScoringPolicy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('career_skill')
            ->where(function ($query) {
                $query
                    ->where(function ($query) {
                        $query
                            ->where('target_level', 70)
                            ->where('importance_weight', 1.10);
                    })
                    ->orWhere(function ($query) {
                        $query
                            ->where('target_level', 75)
                            ->where('importance_weight', 1.20);
                    });
            })
            ->update([
                'target_level' => SkillPathScoringPolicy::FUNCTIONAL_LEVEL,
                'importance_weight' => SkillPathScoringPolicy::DEFAULT_IMPORTANCE_WEIGHT,
                'updated_at' => now(),
            ]);

        DB::table('portfolio_project_skill')
            ->where('required_level', 65)
            ->where('weight', 1.00)
            ->update([
                'required_level' => SkillPathScoringPolicy::FUNCTIONAL_LEVEL,
                'weight' => SkillPathScoringPolicy::DEFAULT_PROJECT_WEIGHT,
                'updated_at' => now(),
            ]);

        $assessments = DB::table('assessments')
            ->whereNotNull('study_program')
            ->where('description', 'like', 'Jawab 25 pertanyaan%')
            ->get([
                'id',
                'study_program',
            ]);

        foreach ($assessments as $assessment) {
            DB::table('assessments')
                ->where('id', $assessment->id)
                ->update([
                    'description' => 'Jawab 27 pertanyaan yang mewakili 9 kemampuan inti jurusan '
                        .$assessment->study_program
                        .', masing-masing 3 pertanyaan untuk setiap kemampuan. Hasil Assesment digunakan untuk memperbarui profil kemampuan awal Anda.',
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void {}
};
