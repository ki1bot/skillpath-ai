<?php

namespace Tests\Feature;

use App\Models\PortfolioProject;
use App\Models\User;
use App\Services\ProjectReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectReadinessEmptyRequirementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_without_required_skills_is_not_ready(): void
    {
        $this->seed();

        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $project = PortfolioProject::query()
            ->where(
                'slug',
                'build-mini-information-system',
            )
            ->firstOrFail();

        $project->skills()->detach();

        $project->unsetRelation('skills');

        $readiness = app(
            ProjectReadinessService::class,
        )->calculate($user, $project);

        $this->assertFalse(
            $readiness['ready'],
        );

        $this->assertSame(
            0.0,
            $readiness['score'],
        );

        $this->assertSame(
            'readiness',
            $readiness['score_type'],
        );

        $this->assertSame(
            'Belum dikonfigurasi',
            $readiness['recommendation']['label'],
        );

        $this->assertSame(
            [],
            $readiness['requirements'],
        );
    }
}
