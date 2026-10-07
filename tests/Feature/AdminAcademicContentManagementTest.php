<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\User;
use Database\Seeders\AcademicStageLearningMaterialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAcademicContentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_update_academic_material_without_multiple_choice_fields(): void
    {
        $material = $this->academicMaterial();

        $originalQuiz = $material->quiz_question;
        $originalSlug = $material->slug;

        $data = $this->materialData($material);

        $data['title'] = 'Latihan Database Management Tahap Amatir';

        $data['practice_task'] = 'Buat tiga tabel yang saling berhubungan, isi dengan data contoh, lalu dokumentasikan hasil pengujian relasinya.';

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.materials.update', $material),
                $data,
            )
            ->assertSessionHasNoErrors();

        $material->refresh();

        $this->assertSame(
            'Latihan Database Management Tahap Amatir',
            $material->title,
        );

        $this->assertSame(
            $data['practice_task'],
            $material->practice_task,
        );

        $this->assertSame(
            $originalQuiz,
            $material->quiz_question,
        );

        $this->assertSame(
            $originalSlug,
            $material->slug,
        );

        $this->assertSame(
            'Amatir',
            $material->difficulty,
        );
    }

    public function test_admin_cannot_move_academic_material_to_another_stage(): void
    {
        $material = $this->academicMaterial();

        $data = $this->materialData($material);

        $data['difficulty'] = 'Ahli';

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.materials.update', $material),
                $data,
            )
            ->assertSessionHasErrors('difficulty');

        $this->assertSame(
            'Amatir',
            $material->fresh()->difficulty,
        );
    }

    public function test_admin_cannot_move_academic_material_to_another_skill(): void
    {
        $material = $this->academicMaterial();

        $otherMaterial = LearningMaterial::query()
            ->where('material_type', 'core')
            ->where('is_active', true)
            ->where('difficulty', 'Amatir')
            ->where('skill_id', '!=', $material->skill_id)
            ->firstOrFail();

        $data = $this->materialData($material);

        $data['skill_id'] = $otherMaterial->skill_id;

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.materials.update', $material),
                $data,
            )
            ->assertSessionHasErrors('skill_id');

        $this->assertSame(
            $material->skill_id,
            $material->fresh()->skill_id,
        );
    }

    public function test_admin_cannot_add_extra_core_material_to_academic_skill(): void
    {
        $material = $this->academicMaterial();

        $data = $this->materialData($material);

        $data['title'] = 'Materi tambahan yang tidak ada dalam katalog';

        $this
            ->actingAs($this->admin)
            ->post(
                route('admin.materials.store'),
                $data,
            )
            ->assertSessionHasErrors('skill_id');
    }

    public function test_admin_cannot_delete_canonical_academic_material(): void
    {
        $material = $this->academicMaterial();

        $this
            ->actingAs($this->admin)
            ->delete(
                route('admin.materials.destroy', $material),
            )
            ->assertSessionHasErrors('material');

        $this->assertDatabaseHas(
            'learning_materials',
            [
                'id' => $material->id,
                'is_active' => true,
            ],
        );
    }

    public function test_admin_can_update_project_description_without_changing_its_three_skills(): void
    {
        $project = $this->academicProject();

        $originalSlug = $project->slug;

        $originalSkillIds = $project
            ->skills
            ->pluck('id')
            ->sort()
            ->values()
            ->all();

        $data = $this->projectData($project);

        $data['problem_statement'] = 'Buat proyek sesuai kasus bidang ini, dokumentasikan hasilnya, dan sertakan pengujian yang dapat diperiksa admin.';

        $data['stretch_features'] = ['', '', ''];

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.projects.update', $project),
                $data,
            )
            ->assertSessionHasNoErrors();

        $project->refresh();

        $this->assertSame(
            $data['problem_statement'],
            $project->problem_statement,
        );

        $this->assertSame(
            $originalSlug,
            $project->slug,
        );

        $this->assertSame(
            $originalSkillIds,
            $project
                ->skills
                ->pluck('id')
                ->sort()
                ->values()
                ->all(),
        );

        $this->assertSame(
            [],
            $project->stretch_features,
        );
    }

    public function test_admin_cannot_add_fourth_project_to_academic_program(): void
    {
        $project = $this->academicProject();

        $data = $this->projectData($project);

        $data['title'] = 'Proyek keempat jurusan';

        $this
            ->actingAs($this->admin)
            ->post(
                route('admin.projects.store'),
                $data,
            )
            ->assertSessionHasErrors('career_id');

        $this->assertSame(
            3,
            PortfolioProject::query()
                ->where('career_id', $project->career_id)
                ->count(),
        );
    }

    public function test_admin_cannot_change_academic_project_skill_relationships(): void
    {
        $project = $this->academicProject();

        $skill = $project->skills->firstOrFail();

        $this
            ->actingAs($this->admin)
            ->post(
                route('admin.projects.skills.store', $project),
                [
                    'skill_id' => $skill->id,
                    'required_level' => 70,
                    'weight' => 1,
                ],
            )
            ->assertSessionHasErrors('skill_id');

        $this
            ->actingAs($this->admin)
            ->delete(
                route(
                    'admin.projects.skills.destroy',
                    [$project, $skill],
                ),
            )
            ->assertSessionHasErrors('skill_id');

        $this->assertCount(
            3,
            $project->fresh()->skills,
        );
    }

    public function test_stage_seeder_preserves_material_changes_made_by_admin(): void
    {
        $material = $this->academicMaterial();

        $material->update([
            'practice_task' => 'Instruksi khusus yang sudah diperbarui admin.',
        ]);

        $this->seed(
            AcademicStageLearningMaterialSeeder::class,
        );

        $this->assertSame(
            'Instruksi khusus yang sudah diperbarui admin.',
            $material->fresh()->practice_task,
        );

        $this->assertSame(
            162,
            LearningMaterial::query()
                ->where('material_type', 'core')
                ->where('is_active', true)
                ->count(),
        );

        $this->assertSame(
            162,
            LearningMaterial::query()
                ->where('material_type', 'reinforcement')
                ->where('is_active', true)
                ->count(),
        );
    }

    private function academicMaterial(): LearningMaterial
    {
        return LearningMaterial::query()
            ->where(
                'slug',
                'belajar-amatir-si-database-management',
            )
            ->firstOrFail();
    }

    private function academicProject(): PortfolioProject
    {
        $career = Career::query()
            ->where('name', 'Sistem Informasi')
            ->firstOrFail();

        return PortfolioProject::query()
            ->where('career_id', $career->id)
            ->with('skills')
            ->firstOrFail();
    }

    /**
     * @return array<string, mixed>
     */
    private function materialData(LearningMaterial $material): array
    {
        return [
            'skill_id' => $material->skill_id,
            'title' => $material->title,
            'summary' => $material->summary,
            'learning_objectives' => $material->learning_objectives,
            'difficulty' => $material->difficulty,
            'estimated_minutes' => $material->estimated_minutes,
            'resource_title' => $material->resource_title,
            'resource_url' => $material->resource_url,
            'practice_task' => $material->practice_task,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function projectData(PortfolioProject $project): array
    {
        return [
            'career_id' => $project->career_id,
            'title' => $project->title,
            'summary' => $project->summary,
            'problem_statement' => $project->problem_statement,
            'difficulty' => $project->difficulty,
            'minimum_features' => $project->minimum_features,
            'stretch_features' => $project->stretch_features ?? [],
            'completion_criteria' => $project->completion_criteria,
            'estimated_hours' => $project->estimated_hours,
        ];
    }
}
