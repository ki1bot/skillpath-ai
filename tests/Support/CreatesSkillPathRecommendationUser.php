<?php

namespace Tests\Support;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\Career;
use App\Models\User;
use App\Models\UserSkill;
use App\Services\RoadmapService;
use Illuminate\Support\Str;

trait CreatesSkillPathRecommendationUser
{
    protected function createSkillPathRecommendationUser(): User
    {
        $career = Career::query()
            ->where('slug', 'sistem-informasi')
            ->firstOrFail();

        $assessment = Assessment::query()
            ->where('career_id', $career->id)
            ->where('study_program', 'Sistem Informasi')
            ->where('is_active', true)
            ->firstOrFail();

        $user = User::factory()->create([
            'name' => 'Pengguna Pengujian SkillPath',
            'email' => 'skillpath-recommendation@example.test',
            'role' => 'student',
            'study_program' => 'Sistem Informasi',
            'semester' => 5,
            'interest_area' => 'Analisis Data dan Pengembangan Sistem',
            'experience' => 'Pengguna khusus pengujian otomatis.',
            'weekly_study_hours' => 8,
            'target_career_id' => $career->id,
            'onboarding_completed_at' => now(),
        ]);

        $scores = [
            'si-sql-data-processing' => 80,
            'si-spreadsheet-data-analysis' => 45,
            'si-business-intelligence-data-visualization' => 55,
            'si-data-visualization' => 60,
            'si-scenario-based-data-analysis' => 50,
            'si-database-management' => 30,
            'si-web-development' => 65,
            'si-system-analysis-design' => 55,
            'si-erd-uml' => 55,
            'si-problem-solving' => 60,
            'si-ui-design' => 50,
            'si-wireframing-prototyping' => 45,
            'si-prototyping' => 50,
            'si-user-research' => 40,
            'si-usability' => 45,
        ];

        $skills = $career
            ->skills()
            ->get()
            ->keyBy('slug');

        $attemptUuid = (string) Str::uuid();

        foreach ($scores as $slug => $score) {
            $skill = $skills->get($slug);

            if (! $skill) {
                continue;
            }

            UserSkill::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'skill_id' => $skill->id,
                ],
                [
                    'score' => $score,
                    'source' => 'assessment',
                    'last_assessed_at' => now()->subDay(),
                ],
            );

            AssessmentResult::create([
                'user_id' => $user->id,
                'assessment_id' => $assessment->id,
                'skill_id' => $skill->id,
                'attempt_uuid' => $attemptUuid,
                'score' => $score,
                'is_correct' => $score >= 70,
                'self_rating' => min($score, 100),
                'answer' => null,
            ]);
        }

        app(RoadmapService::class)->regenerate(
            $user->fresh(['targetCareer']),
            'Skenario pengujian rekomendasi SkillPath',
        );

        return $user->fresh(['targetCareer']);
    }
}
