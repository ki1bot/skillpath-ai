<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\User;
use App\Services\RoadmapService;
use App\Support\AcademicProgramCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadmapAcademicStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_academic_program_generates_three_areas_with_three_core_materials(): void
    {
        $this->seed();

        foreach (array_keys(AcademicProgramCatalog::programs()) as $studyProgram) {
            $career = Career::query()
                ->where('name', $studyProgram)
                ->where('is_active', true)
                ->firstOrFail();

            $user = User::factory()->create([
                'role' => 'student',
                'study_program' => $studyProgram,
                'weekly_study_hours' => 8,
                'target_career_id' => $career->id,
                'onboarding_completed_at' => now(),
            ]);

            $roadmap = app(RoadmapService::class)->regenerate(
                $user->fresh([
                    'targetCareer',
                ]),
                'Pengujian struktur akademik roadmap',
            );

            $roadmap->load(
                'items.material.skill',
            );

            $coreItems = $roadmap
                ->items
                ->filter(
                    fn ($item) => $item
                        ->material
                        ->material_type === 'core',
                )
                ->sortBy('position')
                ->values();

            $this->assertCount(
                9,
                $coreItems,
                "Roadmap {$studyProgram} harus memiliki tepat 9 materi utama.",
            );

            $expectedSkillSlugs = collect(
                AcademicProgramCatalog::skillSlugs(
                    $studyProgram,
                ),
            )
                ->sort()
                ->values()
                ->all();

            $actualSkillSlugs = $coreItems
                ->map(
                    fn ($item) => $item
                        ->material
                        ->skill
                        ->slug,
                )
                ->sort()
                ->values()
                ->all();

            $this->assertSame(
                $expectedSkillSlugs,
                $actualSkillSlugs,
                "Roadmap {$studyProgram} harus memuat seluruh 9 skill akademik tanpa skill yang hilang atau salah jurusan.",
            );

            $stages = $coreItems->groupBy('stage');

            $this->assertCount(
                3,
                $stages,
                "Roadmap {$studyProgram} harus memiliki tepat 3 bidang.",
            );

            foreach ($stages as $stageItems) {
                $this->assertCount(
                    3,
                    $stageItems,
                    "Setiap bidang roadmap {$studyProgram} harus berisi tepat 3 materi utama.",
                );

                $stageTitle = (string) $stageItems
                    ->first()
                    ->stage_title;

                $expectedAreaSlugs = collect(
                    AcademicProgramCatalog::areaSkillSlugs(
                        $studyProgram,
                        $stageTitle,
                    ),
                )
                    ->sort()
                    ->values()
                    ->all();

                $this->assertCount(
                    3,
                    $expectedAreaSlugs,
                    "Bidang {$stageTitle} tidak sesuai dengan katalog jurusan {$studyProgram}.",
                );

                $actualAreaSlugs = $stageItems
                    ->map(
                        fn ($item) => $item
                            ->material
                            ->skill
                            ->slug,
                    )
                    ->sort()
                    ->values()
                    ->all();

                $this->assertSame(
                    $expectedAreaSlugs,
                    $actualAreaSlugs,
                    "Materi pada bidang {$stageTitle} jurusan {$studyProgram} tidak sesuai katalog akademik.",
                );
            }

            $positions = $coreItems
                ->pluck('position')
                ->map(
                    fn ($position) => (int) $position,
                )
                ->values()
                ->all();

            $this->assertSame(
                range(1, 9),
                $positions,
                "Posisi materi roadmap {$studyProgram} harus berurutan dari 1 sampai 9.",
            );

            $this->assertSame(
                'available',
                $coreItems
                    ->first()
                    ->status,
                "Materi pertama {$studyProgram} harus tersedia pada awal roadmap.",
            );

            foreach ($coreItems->slice(1) as $item) {
                $this->assertSame(
                    'locked',
                    $item->status,
                    "Materi setelah materi pertama pada {$studyProgram} harus terkunci.",
                );
            }
        }
    }
}
