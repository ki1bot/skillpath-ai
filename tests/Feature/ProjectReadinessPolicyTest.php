<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\PortfolioProject;
use App\Models\User;
use App\Models\UserSkill;
use App\Services\ProjectReadinessService;
use App\Support\SkillPathScoringPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectReadinessPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private PortfolioProject $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $career = Career::query()
            ->where('slug', 'sistem-informasi')
            ->firstOrFail();

        $this->user = User::factory()->create([
            'name' => 'Pengguna Pengujian Kesiapan Proyek',
            'email' => 'project-readiness@example.test',
            'role' => 'student',
            'study_program' => 'Sistem Informasi',
            'semester' => 5,
            'interest_area' => 'Analisis Data dan Pengembangan Sistem',
            'experience' => 'Pengguna khusus pengujian kebijakan kesiapan proyek.',
            'weekly_study_hours' => 8,
            'target_career_id' => $career->id,
            'onboarding_completed_at' => now(),
        ]);

        $this->project = PortfolioProject::query()
            ->where('slug', 'build-mini-information-system')
            ->with('skills')
            ->firstOrFail();
    }

    public function test_project_is_recommended_when_all_required_skills_reach_functional_level(): void
    {
        $this->setAllProjectSkillScores(
            SkillPathScoringPolicy::FUNCTIONAL_LEVEL,
        );

        $readiness = app(ProjectReadinessService::class)
            ->calculate(
                $this->user,
                $this->project,
            );

        $this->assertTrue($readiness['ready']);
        $this->assertSame('recommended', $readiness['recommendation']['level']);
        $this->assertSame('readiness', $readiness['score_type']);
        $this->assertFalse($readiness['quality_assessed']);
        $this->assertSame(0, $readiness['missing_count']);
    }

    public function test_project_requires_strengthening_when_every_skill_has_emerging_evidence_but_not_all_are_functional(): void
    {
        $this->setAllProjectSkillScores(
            SkillPathScoringPolicy::EMERGING_LEVEL,
        );

        $firstSkill = $this->project->skills->first();

        $this->assertNotNull($firstSkill);

        UserSkill::query()
            ->where('user_id', $this->user->id)
            ->where('skill_id', $firstSkill->id)
            ->update([
                'score' => SkillPathScoringPolicy::FUNCTIONAL_LEVEL,
            ]);

        $readiness = app(ProjectReadinessService::class)
            ->calculate(
                $this->user,
                $this->project,
            );

        $this->assertFalse($readiness['ready']);
        $this->assertSame('strengthen', $readiness['recommendation']['level']);
        $this->assertGreaterThan(0, $readiness['missing_count']);
    }

    public function test_project_is_challenge_when_a_required_skill_has_no_emerging_evidence(): void
    {
        $this->setAllProjectSkillScores(
            SkillPathScoringPolicy::EMERGING_LEVEL,
        );

        $firstSkill = $this->project->skills->first();

        $this->assertNotNull($firstSkill);

        UserSkill::query()
            ->where('user_id', $this->user->id)
            ->where('skill_id', $firstSkill->id)
            ->update([
                'score' => 0,
            ]);

        $readiness = app(ProjectReadinessService::class)
            ->calculate(
                $this->user,
                $this->project,
            );

        $this->assertFalse($readiness['ready']);
        $this->assertSame('challenge', $readiness['recommendation']['level']);
        $this->assertGreaterThan(0, $readiness['missing_count']);
    }

    private function setAllProjectSkillScores(
        float $score,
    ): void {
        foreach ($this->project->skills as $skill) {
            UserSkill::updateOrCreate(
                [
                    'user_id' => $this->user->id,
                    'skill_id' => $skill->id,
                ],
                [
                    'score' => $score,
                    'source' => 'assessment',
                    'last_assessed_at' => now(),
                ],
            );
        }
    }
}
