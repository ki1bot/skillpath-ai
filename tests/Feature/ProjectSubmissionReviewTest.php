<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\PortfolioProject;
use App\Models\User;
use App\Models\UserProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProjectSubmissionReviewTest extends TestCase
{
    use RefreshDatabase;

    private const DRIVE_URL = 'https://drive.google.com/drive/folders/project-evidence';

    private User $user;

    private PortfolioProject $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $career = Career::query()
            ->where(
                'slug',
                'sistem-informasi',
            )
            ->firstOrFail();

        $this->user = User::factory()->create([
            'name' => 'Pengguna Pengujian Proyek',
            'email' => 'project-submission@example.test',
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
            ->where(
                'slug',
                'build-mini-information-system',
            )
            ->firstOrFail();

        $this->startProject();
    }

    public function test_project_submission_waits_for_admin_review(): void
    {
        $this->submitProject();

        $userProject = $this->userProject();

        $this->assertSame(
            'submitted',
            $userProject->status,
        );

        $this->assertSame(
            'pending',
            $userProject->review_status,
        );

        $this->assertSame(
            95,
            (int) $userProject->progress_percentage,
        );

        $this->assertNull(
            $userProject->evaluation_score,
        );

        $this->assertNull(
            $userProject->completed_at,
        );

        $this->assertSame(
            self::DRIVE_URL,
            $userProject->repository_url,
        );
    }

    public function test_pending_project_cannot_be_submitted_twice(): void
    {
        $this->submitProject();

        $this->actingAs(
            $this->user,
        )
            ->patch(
                route(
                    'projects.update',
                    $this->project,
                ),
                [
                    'repository_url' => 'https://drive.google.com/drive/folders/second-project-evidence',
                ],
            )
            ->assertSessionHasErrors([
                'repository_url',
            ]);
    }

    public function test_admin_can_see_project_submission(): void
    {
        $this->submitProject();

        $this->actingAs(
            $this->admin(),
        )
            ->get(
                route(
                    'admin.project-submissions.index',
                ),
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component(
                        'admin/project-submissions',
                    )
                    ->where(
                        'submissions.data.0.project.title',
                        $this->project->title,
                    )
                    ->where(
                        'submissions.data.0.repository_url',
                        self::DRIVE_URL,
                    )
                    ->where(
                        'submissions.data.0.review_status',
                        'pending',
                    ),
            );
    }

    public function test_student_cannot_open_project_submission_management(): void
    {
        $this->actingAs(
            $this->user,
        )
            ->get(
                route(
                    'admin.project-submissions.index',
                ),
            )
            ->assertForbidden();
    }

    public function test_admin_score_80_completes_project(): void
    {
        $this->submitProject();

        $userProject = $this->userProject();

        $this->actingAs(
            $this->admin(),
        )
            ->patch(
                route(
                    'admin.project-submissions.update',
                    $userProject,
                ),
                [
                    'score' => 80,
                    'admin_notes' => 'Seluruh bagian wajib sudah terpenuhi.',
                ],
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $userProject->refresh();

        $this->assertSame(
            'completed',
            $userProject->status,
        );

        $this->assertSame(
            'reviewed',
            $userProject->review_status,
        );

        $this->assertSame(
            80.0,
            (float) $userProject->evaluation_score,
        );

        $this->assertSame(
            100,
            (int) $userProject->progress_percentage,
        );

        $this->assertNotNull(
            $userProject->completed_at,
        );

        $this->assertNotNull(
            $userProject->reviewed_at,
        );
    }

    public function test_admin_score_79_requires_revision(): void
    {
        $this->submitProject();

        $userProject = $this->userProject();

        $this->actingAs(
            $this->admin(),
        )
            ->patch(
                route(
                    'admin.project-submissions.update',
                    $userProject,
                ),
                [
                    'score' => 79,
                    'admin_notes' => 'Dokumentasi dan pengujian masih perlu diperbaiki.',
                ],
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $userProject->refresh();

        $this->assertSame(
            'needs_revision',
            $userProject->status,
        );

        $this->assertSame(
            'reviewed',
            $userProject->review_status,
        );

        $this->assertSame(
            79.0,
            (float) $userProject->evaluation_score,
        );

        $this->assertSame(
            95,
            (int) $userProject->progress_percentage,
        );

        $this->assertNull(
            $userProject->completed_at,
        );
    }

    public function test_failed_project_can_be_submitted_again(): void
    {
        $this->submitProject();

        $userProject = $this->userProject();

        $this->actingAs(
            $this->admin(),
        )
            ->patch(
                route(
                    'admin.project-submissions.update',
                    $userProject,
                ),
                [
                    'score' => 79,
                ],
            )
            ->assertSessionHasNoErrors();

        $this->actingAs(
            $this->user,
        )
            ->patch(
                route(
                    'projects.update',
                    $this->project,
                ),
                [
                    'repository_url' => 'https://drive.google.com/drive/folders/project-revision',
                ],
            )
            ->assertSessionHasNoErrors();

        $userProject->refresh();

        $this->assertSame(
            'submitted',
            $userProject->status,
        );

        $this->assertSame(
            'pending',
            $userProject->review_status,
        );

        $this->assertSame(
            'https://drive.google.com/drive/folders/project-revision',
            $userProject->repository_url,
        );
    }

    public function test_reviewed_project_cannot_be_scored_twice_without_resubmission(): void
    {
        $this->submitProject();

        $userProject = $this->userProject();

        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(
                route(
                    'admin.project-submissions.update',
                    $userProject,
                ),
                [
                    'score' => 80,
                ],
            )
            ->assertSessionHasNoErrors();

        $this->actingAs($admin)
            ->patch(
                route(
                    'admin.project-submissions.update',
                    $userProject,
                ),
                [
                    'score' => 30,
                ],
            )
            ->assertSessionHasErrors([
                'score',
            ]);

        $userProject->refresh();

        $this->assertSame(
            80.0,
            (float) $userProject->evaluation_score,
        );
    }

    private function startProject(): void
    {
        $this->actingAs(
            $this->user,
        )
            ->post(
                route(
                    'projects.start',
                    $this->project,
                ),
            )
            ->assertSessionHasNoErrors();
    }

    private function submitProject(): void
    {
        $this->actingAs(
            $this->user,
        )
            ->patch(
                route(
                    'projects.update',
                    $this->project,
                ),
                [
                    'repository_url' => self::DRIVE_URL,
                ],
            )
            ->assertSessionHasNoErrors();
    }

    private function userProject(): UserProject
    {
        return UserProject::query()
            ->where(
                'user_id',
                $this->user->id,
            )
            ->where(
                'portfolio_project_id',
                $this->project->id,
            )
            ->firstOrFail();
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }
}
