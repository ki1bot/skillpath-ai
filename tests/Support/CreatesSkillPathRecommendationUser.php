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
            ->where('slug', 'backend-developer')
            ->firstOrFail();

        $assessment = Assessment::query()
            ->where('career_id', $career->id)
            ->firstOrFail();

        $user = User::factory()->create([
            'name' => 'Pengguna Pengujian SkillPath',
            'email' => 'skillpath-recommendation@example.test',
            'role' => 'student',
            'study_program' => 'Sistem Informasi',
            'semester' => 5,
            'interest_area' => 'Backend dan pengembangan produk web',
            'experience' => 'Pengguna khusus pengujian otomatis.',
            'weekly_study_hours' => 8,
            'target_career_id' => $career->id,
            'onboarding_completed_at' => now(),
        ]);

        $scores = [
            'programming-fundamentals' => 80,
            'git-github' => 45,
            'terminal-cli' => 55,
            'http-fundamentals' => 70,
            'database-fundamentals' => 30,
            'sql' => 55,
            'php-laravel' => 65,
            'rest-api' => 55,
            'authentication-authorization' => 50,
            'validation-error-handling' => 45,
            'testing-fundamentals' => 45,
            'deployment-basics' => 10,
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
