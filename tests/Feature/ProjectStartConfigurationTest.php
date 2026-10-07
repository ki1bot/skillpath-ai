<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\PortfolioProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectStartConfigurationTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private PortfolioProject $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $career = Career::query()
            ->where('name', 'Sistem Informasi')
            ->firstOrFail();

        $this->student = User::factory()->create([
            'role' => 'student',
            'study_program' => 'Sistem Informasi',
            'semester' => 5,
            'interest_area' => 'Pengembangan Sistem',
            'experience' => 'Pengguna pengujian proyek.',
            'weekly_study_hours' => 8,
            'target_career_id' => $career->id,
            'onboarding_completed_at' => now(),
            'email_verified_at' => now(),
        ]);

        $this->project = PortfolioProject::query()
            ->where('slug', 'build-mini-information-system')
            ->firstOrFail();
    }

    public function test_project_without_skills_cannot_be_started(): void
    {
        $this->project->skills()->detach();

        $this
            ->actingAs($this->student)
            ->post(
                route(
                    'projects.start',
                    $this->project,
                ),
            )
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseMissing(
            'user_projects',
            [
                'user_id' => $this->student->id,
                'portfolio_project_id' => $this->project->id,
            ],
        );

        $this->assertDatabaseMissing(
            'progress_logs',
            [
                'user_id' => $this->student->id,
                'activity_type' => 'project_started',
            ],
        );
    }

    public function test_project_with_skills_can_be_started(): void
    {
        $this->assertCount(
            3,
            $this->project->skills,
        );

        $this
            ->actingAs($this->student)
            ->post(
                route(
                    'projects.start',
                    $this->project,
                ),
            )
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas(
            'user_projects',
            [
                'user_id' => $this->student->id,
                'portfolio_project_id' => $this->project->id,
                'status' => 'in_progress',
                'review_status' => 'not_submitted',
            ],
        );

        $this->assertDatabaseHas(
            'progress_logs',
            [
                'user_id' => $this->student->id,
                'activity_type' => 'project_started',
            ],
        );
    }
}
