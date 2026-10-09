<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Roadmap;
use App\Models\User;
use App\Support\AcademicProgramCatalog;
use Database\Seeders\AcademicTaskRevisionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicTaskRevisionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_revised_materials_follow_their_study_program_and_skill(): void
    {
        $this->seed(AcademicTaskRevisionSeeder::class);

        $materialCount = 0;

        foreach (AcademicProgramCatalog::programs() as $programName => $program) {
            foreach ($program['areas'] as $area) {
                foreach ($area['skills'] as $skillDefinition) {
                    $skillSlug = (string) $skillDefinition['slug'];

                    foreach (['amatir', 'menengah', 'ahli'] as $stage) {
                        $material = LearningMaterial::query()
                            ->with('skill')
                            ->where(
                                'slug',
                                'belajar-'.$stage.'-'.$skillSlug,
                            )
                            ->firstOrFail();

                        $reinforcement = LearningMaterial::query()
                            ->where(
                                'slug',
                                'penguatan-'.$stage.'-'.$skillSlug,
                            )
                            ->firstOrFail();

                        $this->assertSame(
                            $skillSlug,
                            $material->skill->slug,
                        );

                        $this->assertSame(
                            'core',
                            $material->material_type,
                        );

                        $this->assertSame(
                            'reinforcement',
                            $reinforcement->material_type,
                        );

                        $this->assertSame(
                            $material->id,
                            $reinforcement->reinforcement_for_material_id,
                        );

                        $this->assertStringContainsString(
                            'Jurusan: '.$programName,
                            $material->practice_task,
                        );

                        $this->assertStringContainsString(
                            'Bidang: '.$area['name'],
                            $material->practice_task,
                        );

                        $this->assertStringContainsString(
                            'Jurusan: '.$programName,
                            $reinforcement->practice_task,
                        );

                        $materialCount++;
                    }
                }
            }
        }

        $this->assertSame(162, $materialCount);
    }

    public function test_revised_projects_follow_their_study_program_and_area(): void
    {
        $this->seed(AcademicTaskRevisionSeeder::class);

        $projectCount = 0;

        foreach (AcademicProgramCatalog::programs() as $programName => $program) {
            $career = Career::query()
                ->where('name', $programName)
                ->firstOrFail();

            $projects = PortfolioProject::query()
                ->where('career_id', $career->id)
                ->with('skills')
                ->get();

            $this->assertCount(3, $projects);

            foreach ($program['areas'] as $area) {
                $expectedSkills = AcademicProgramCatalog::areaSkillSlugs(
                    (string) $programName,
                    (string) $area['name'],
                );

                sort($expectedSkills);

                $matchingProjects = $projects
                    ->filter(function (PortfolioProject $project) use (
                        $expectedSkills,
                    ): bool {
                        return $project
                            ->skills
                            ->pluck('slug')
                            ->sort()
                            ->values()
                            ->all() === $expectedSkills;
                    });

                $this->assertCount(1, $matchingProjects);

                $project = $matchingProjects->first();

                $this->assertNotEmpty(
                    trim((string) $project->problem_statement),
                );

                $this->assertNotEmpty(
                    $project->minimum_features,
                );

                $this->assertNotEmpty(
                    $project->completion_criteria,
                );

                $projectCount++;
            }
        }

        $this->assertSame(18, $projectCount);
    }

    public function test_reviser_preserves_custom_admin_assignments(): void
    {
        $material = LearningMaterial::query()
            ->where(
                'slug',
                'belajar-amatir-si-database-management',
            )
            ->firstOrFail();

        $project = PortfolioProject::query()
            ->where(
                'slug',
                'build-mini-information-system',
            )
            ->firstOrFail();

        $materialText = 'Tugas praktik khusus yang telah dibuat administrator.';

        $projectText = 'Kasus proyek khusus yang telah dibuat administrator.';

        $material->update([
            'practice_task' => $materialText,
        ]);

        $project->update([
            'problem_statement' => $projectText,
        ]);

        $this->seed(AcademicTaskRevisionSeeder::class);

        $this->assertSame(
            $materialText,
            $material->fresh()->practice_task,
        );

        $this->assertSame(
            $projectText,
            $project->fresh()->problem_statement,
        );
    }

    public function test_reviser_is_idempotent(): void
    {
        $this->seed(AcademicTaskRevisionSeeder::class);

        $material = LearningMaterial::query()
            ->where(
                'slug',
                'belajar-menengah-psi-counseling-skills',
            )
            ->firstOrFail();

        $project = PortfolioProject::query()
            ->where(
                'slug',
                'counseling-case-simulation',
            )
            ->firstOrFail();

        $materialText = $material->practice_task;
        $projectText = $project->problem_statement;

        $this->seed(AcademicTaskRevisionSeeder::class);

        $this->assertSame(
            $materialText,
            $material->fresh()->practice_task,
        );

        $this->assertSame(
            $projectText,
            $project->fresh()->problem_statement,
        );
    }

    public function test_student_cannot_open_material_from_another_study_program(): void
    {
        $career = Career::query()
            ->where('name', 'Psikologi')
            ->firstOrFail();

        $student = $this->studentForCareer($career);

        $material = LearningMaterial::query()
            ->where(
                'slug',
                'belajar-amatir-si-sql-data-processing',
            )
            ->firstOrFail();

        $roadmap = Roadmap::create([
            'user_id' => $student->id,
            'career_id' => $career->id,
            'version' => 1,
            'reason' => 'Pengujian materi lintas jurusan',
            'estimated_weeks' => 3,
            'is_active' => true,
        ]);

        $roadmap->items()->create([
            'learning_material_id' => $material->id,
            'stage' => 1,
            'stage_title' => 'Amatir',
            'position' => 1,
            'status' => 'available',
            'progress_percentage' => 0,
        ]);

        $this
            ->actingAs($student)
            ->get(route('roadmap.material', $material))
            ->assertNotFound();
    }

    public function test_student_cannot_open_project_from_another_study_program(): void
    {
        $career = Career::query()
            ->where('name', 'Psikologi')
            ->firstOrFail();

        $student = $this->studentForCareer($career);

        $project = PortfolioProject::query()
            ->where(
                'slug',
                'build-mini-information-system',
            )
            ->firstOrFail();

        $this
            ->actingAs($student)
            ->get(route('projects.show', $project))
            ->assertNotFound();
    }

    public function test_project_with_incorrect_skill_mapping_cannot_be_opened(): void
    {
        $career = Career::query()
            ->where('name', 'Sistem Informasi')
            ->firstOrFail();

        $student = $this->studentForCareer($career);

        $project = PortfolioProject::query()
            ->where(
                'slug',
                'build-mini-information-system',
            )
            ->firstOrFail();

        $project->skills()->detach();

        $this
            ->actingAs($student)
            ->get(route('projects.show', $project))
            ->assertNotFound();
    }

    private function studentForCareer(Career $career): User
    {
        return User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
            'study_program' => $career->name,
            'target_career_id' => $career->id,
            'semester' => 5,
            'interest_area' => 'Pembelajaran akademik',
            'experience' => 'Pengguna pengujian.',
            'weekly_study_hours' => 8,
            'onboarding_completed_at' => now(),
        ]);
    }
}
