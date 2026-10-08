<?php

namespace Tests\Feature;

use App\Models\LearningMaterial;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicStageSeederIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_reseeding_restores_staged_materials_and_preserves_admin_changes(): void
    {
        $this->seed();

        $material = LearningMaterial::query()
            ->where(
                'slug',
                'belajar-amatir-si-database-management',
            )
            ->firstOrFail();

        $originalId = $material->id;

        $customTask = 'Instruksi khusus yang telah diperbarui oleh admin.';

        $material->update([
            'practice_task' => $customTask,
        ]);

        $this->seed(
            DatabaseSeeder::class,
        );

        $material->refresh();

        $this->assertSame(
            $originalId,
            $material->id,
        );

        $this->assertTrue(
            $material->is_active,
        );

        $this->assertSame(
            $customTask,
            $material->practice_task,
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
}
