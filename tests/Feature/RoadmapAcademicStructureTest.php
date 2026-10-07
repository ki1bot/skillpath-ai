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

    public function test_each_academic_program_generates_three_learning_stages_with_nine_core_materials_each(): void
    {
        $this->seed();

        foreach (
            array_keys(
                AcademicProgramCatalog::programs(),
            ) as $studyProgram
        ) {
            $career = Career::query()
                ->where(
                    'name',
                    $studyProgram,
                )
                ->where(
                    'is_active',
                    true,
                )
                ->firstOrFail();

            $user = User::factory()->create([
                'role' => 'student',
                'study_program' => $studyProgram,
                'weekly_study_hours' => 8,
                'target_career_id' => $career->id,
                'onboarding_completed_at' => now(),
            ]);

            $roadmap = app(
                RoadmapService::class,
            )->regenerate(
                $user->fresh([
                    'targetCareer',
                ]),
                'Pengujian struktur tiga tahap roadmap',
            );

            $roadmap->load(
                'items.material.skill',
            );

            $coreItems = $roadmap
                ->items
                ->filter(
                    fn ($item) => (
                        $item
                            ->material
                            ->material_type
                        === 'core'
                    ),
                )
                ->sortBy(
                    'position',
                )
                ->values();

            $this->assertCount(
                27,
                $coreItems,
                "Roadmap {$studyProgram} harus memiliki tepat 27 materi utama.",
            );

            $stages = $coreItems
                ->groupBy(
                    'stage',
                );

            $this->assertCount(
                3,
                $stages,
                "Roadmap {$studyProgram} harus memiliki tepat 3 tahap.",
            );

            $expectedStages = [
                1 => 'Amatir',
                2 => 'Menengah',
                3 => 'Ahli',
            ];

            $expectedSkillSlugs = collect(
                AcademicProgramCatalog::skillSlugs(
                    $studyProgram,
                ),
            )
                ->sort()
                ->values()
                ->all();

            foreach (
                $expectedStages as $stage => $stageTitle
            ) {
                $stageItems = $stages->get(
                    $stage,
                );

                $this->assertNotNull(
                    $stageItems,
                    "Tahap {$stageTitle} belum tersedia pada {$studyProgram}.",
                );

                $this->assertCount(
                    9,
                    $stageItems,
                    "Tahap {$stageTitle} pada {$studyProgram} harus memiliki tepat 9 materi.",
                );

                $this->assertSame(
                    $stageTitle,
                    (string) $stageItems
                        ->first()
                        ->stage_title,
                );

                $actualSkillSlugs = $stageItems
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
                    "Tahap {$stageTitle} pada {$studyProgram} harus memuat seluruh 9 skill jurusan.",
                );

                foreach ($stageItems as $item) {
                    $this->assertSame(
                        $stageTitle,
                        $item
                            ->material
                            ->difficulty,
                        "Materi {$item->material->title} harus mempunyai tingkat {$stageTitle}.",
                    );
                }

                $program = AcademicProgramCatalog::program(
                    $studyProgram,
                );

                $this->assertNotNull(
                    $program,
                );

                foreach (
                    $program[
                        'areas'
                    ] as $area
                ) {
                    $areaSkillSlugs = collect(
                        $area[
                            'skills'
                        ],
                    )
                        ->pluck(
                            'slug',
                        )
                        ->values();

                    $matchingSkills = $stageItems
                        ->filter(
                            fn ($item) => $areaSkillSlugs
                                ->contains(
                                    $item
                                        ->material
                                        ->skill
                                        ->slug,
                                ),
                        );

                    $this->assertCount(
                        3,
                        $matchingSkills,
                        "Bidang {$area['name']} pada tahap {$stageTitle} jurusan {$studyProgram} harus memiliki tepat 3 materi.",
                    );
                }
            }

            $positions = $coreItems
                ->pluck(
                    'position',
                )
                ->map(
                    fn ($position) => (
                        (int) $position
                    ),
                )
                ->values()
                ->all();

            $this->assertSame(
                range(
                    1,
                    27,
                ),
                $positions,
                "Posisi materi roadmap {$studyProgram} harus berurutan dari 1 sampai 27.",
            );

            $this->assertSame(
                'available',
                $coreItems
                    ->first()
                    ->status,
                "Materi pertama {$studyProgram} harus tersedia pada awal roadmap.",
            );

            foreach (
                $coreItems->slice(
                    1,
                ) as $item
            ) {
                $this->assertSame(
                    'locked',
                    $item->status,
                    "Hanya materi pertama {$studyProgram} yang boleh tersedia sebelum ada materi yang lulus.",
                );
            }

            $tasksBySkill = $coreItems
                ->groupBy(
                    fn ($item) => $item
                        ->material
                        ->skill
                        ->slug,
                );

            foreach (
                $tasksBySkill as $skillSlug => $skillItems
            ) {
                $this->assertCount(
                    3,
                    $skillItems,
                );

                $this->assertCount(
                    3,
                    $skillItems
                        ->pluck(
                            'material.practice_task',
                        )
                        ->unique()
                        ->values(),
                    "Tugas Amatir, Menengah, dan Ahli untuk {$skillSlug} harus berbeda.",
                );
            }
        }
    }
}
