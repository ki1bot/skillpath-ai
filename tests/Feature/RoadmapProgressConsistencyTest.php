<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\Roadmap;
use App\Models\User;
use App\Services\CareerReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RoadmapProgressConsistencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_readiness_and_student_dashboard_exclude_reinforcement_from_roadmap_completion(): void
    {
        $career = Career::query()
            ->where('name', 'Sistem Informasi')
            ->where('is_active', true)
            ->firstOrFail();

        $user = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
            'study_program' => $career->name,
            'target_career_id' => $career->id,
            'weekly_study_hours' => 8,
            'onboarding_completed_at' => now(),
        ]);

        $materials = LearningMaterial::query()
            ->where('material_type', 'core')
            ->where('is_active', true)
            ->whereIn(
                'skill_id',
                $career->skills()->pluck('skills.id'),
            )
            ->orderBy('id')
            ->limit(2)
            ->get();

        $this->assertCount(2, $materials);

        $reinforcement = LearningMaterial::query()
            ->where('material_type', 'reinforcement')
            ->where('is_active', true)
            ->where(
                'reinforcement_for_material_id',
                $materials->first()->id,
            )
            ->firstOrFail();

        $roadmap = Roadmap::create([
            'user_id' => $user->id,
            'career_id' => $career->id,
            'version' => 1,
            'reason' => 'Pengujian perhitungan progres',
            'estimated_weeks' => 3,
            'is_active' => true,
        ]);

        $roadmap->items()->create([
            'learning_material_id' => $materials->first()->id,
            'stage' => 1,
            'stage_title' => 'Amatir',
            'position' => 1,
            'status' => 'completed',
            'progress_percentage' => 100,
            'evaluation_score' => 70,
            'completed_at' => now(),
        ]);

        $roadmap->items()->create([
            'learning_material_id' => $materials->last()->id,
            'stage' => 1,
            'stage_title' => 'Amatir',
            'position' => 2,
            'status' => 'locked',
            'progress_percentage' => 0,
        ]);

        $roadmap->items()->create([
            'learning_material_id' => $reinforcement->id,
            'stage' => 1,
            'stage_title' => 'Amatir',
            'position' => 3,
            'status' => 'available',
            'progress_percentage' => 0,
        ]);

        $readiness = app(
            CareerReadinessService::class,
        )->calculate($user);

        $this->assertSame(
            50.0,
            $readiness['roadmap_completion'],
        );

        $this
            ->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('dashboard')
                    ->where('roadmap.total', 2)
                    ->where('roadmap.completed', 1)
                    ->where('readiness.roadmap_completion', 50),
            );
    }

    public function test_admin_dashboard_counts_only_active_materials_in_its_summary(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $material = LearningMaterial::query()
            ->where('material_type', 'core')
            ->where('is_active', true)
            ->firstOrFail();

        $material->update([
            'is_active' => false,
        ]);

        $totalMaterials = LearningMaterial::query()->count();

        $activeMaterials = LearningMaterial::query()
            ->where('is_active', true)
            ->count();

        $activeCoreMaterials = LearningMaterial::query()
            ->where('is_active', true)
            ->where('material_type', 'core')
            ->count();

        $activeReinforcementMaterials = LearningMaterial::query()
            ->where('is_active', true)
            ->where('material_type', 'reinforcement')
            ->count();

        $inactiveMaterials = LearningMaterial::query()
            ->where('is_active', false)
            ->count();

        $this->assertSame(323, $activeMaterials);
        $this->assertSame(161, $activeCoreMaterials);
        $this->assertSame(162, $activeReinforcementMaterials);

        $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('admin/index')
                    ->where('stats.materials', $activeMaterials)
                    ->has('materials', $totalMaterials)
                    ->where(
                        'materials',
                        function ($items) use (
                            $activeMaterials,
                            $activeCoreMaterials,
                            $activeReinforcementMaterials,
                            $inactiveMaterials,
                        ): bool {
                            $materials = collect($items);

                            return $materials
                                ->where('is_active', true)
                                ->count() === $activeMaterials
                                && $materials
                                    ->where('is_active', true)
                                    ->where('material_type', 'core')
                                    ->count() === $activeCoreMaterials
                                && $materials
                                    ->where('is_active', true)
                                    ->where('material_type', 'reinforcement')
                                    ->count() === $activeReinforcementMaterials
                                && $materials
                                    ->where('is_active', false)
                                    ->count() === $inactiveMaterials;
                        },
                    ),
            );
    }
}
