<?php

namespace App\Services;

use App\Models\LearningMaterial;
use App\Models\ProgressLog;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Models\Skill;
use App\Models\User;
use App\Support\AcademicProgramCatalog;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RoadmapService
{
    public function __construct(
        private readonly SkillGapService $skillGapService,
    ) {}

    public function regenerate(
        User $user,
        string $reason = 'Assessment awal',
    ): Roadmap {
        $user->loadMissing(
            'targetCareer',
        );

        if (! $user->targetCareer) {
            throw new RuntimeException(
                'Target karier belum dipilih.',
            );
        }

        return DB::transaction(
            function () use (
                $user,
                $reason,
            ) {
                $studyProgram = (string) $user
                    ->targetCareer
                    ->name;

                $program = AcademicProgramCatalog::program(
                    $studyProgram,
                );

                if ($program === null) {
                    throw new RuntimeException(
                        'Struktur akademik untuk jurusan '.$studyProgram.' belum tersedia.',
                    );
                }

                $analysis = collect(
                    $this
                        ->skillGapService
                        ->analyze($user),
                )->keyBy(
                    'skill_id',
                );

                $skillSlugs = AcademicProgramCatalog::skillSlugs(
                    $studyProgram,
                );

                $skills = Skill::query()
                    ->whereIn(
                        'slug',
                        $skillSlugs,
                    )
                    ->with(
                        'prerequisites:id,name,slug',
                    )
                    ->get()
                    ->keyBy(
                        'slug',
                    );

                if (
                    $skills->count()
                    !== count($skillSlugs)
                ) {
                    throw new RuntimeException(
                        'Struktur skill untuk jurusan '.$studyProgram.' belum lengkap.',
                    );
                }

                $materialsBySkill = LearningMaterial::query()
                    ->whereIn(
                        'skill_id',
                        $skills
                            ->pluck('id')
                            ->all(),
                    )
                    ->where(
                        'material_type',
                        'core',
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->orderBy(
                        'id',
                    )
                    ->get()
                    ->groupBy(
                        'skill_id',
                    );

                $areas = collect(
                    $program['areas'],
                )
                    ->values()
                    ->map(
                        function (
                            array $area,
                            int $areaIndex,
                        ) use (
                            $skills,
                            $materialsBySkill,
                            $analysis,
                            $studyProgram,
                        ): array {
                            $items = collect(
                                $area['skills'],
                            )
                                ->values()
                                ->map(
                                    function (
                                        array $skillDefinition,
                                        int $catalogIndex,
                                    ) use (
                                        $skills,
                                        $materialsBySkill,
                                        $analysis,
                                        $studyProgram,
                                        $area,
                                    ): array {
                                        $skillSlug = (string) $skillDefinition[
                                            'slug'
                                        ];

                                        $skill = $skills->get(
                                            $skillSlug,
                                        );

                                        if (! $skill) {
                                            throw new RuntimeException(
                                                'Skill '.$skillSlug.' pada bidang '.$area['name'].' jurusan '.$studyProgram.' belum tersedia.',
                                            );
                                        }

                                        $material = $materialsBySkill
                                            ->get(
                                                $skill->id,
                                                collect(),
                                            )
                                            ->first();

                                        if (! $material) {
                                            throw new RuntimeException(
                                                'Materi utama untuk skill '.$skill->name.' belum tersedia.',
                                            );
                                        }

                                        $analysisItem = $analysis->get(
                                            $skill->id,
                                        );

                                        return [
                                            'material' => $material,
                                            'priority' => is_array(
                                                $analysisItem,
                                            )
                                                ? (float) (
                                                    $analysisItem[
                                                        'priority'
                                                    ] ?? 0
                                                )
                                                : 0.0,
                                            'catalog_index' => $catalogIndex,
                                        ];
                                    },
                                )
                                ->sort(
                                    function (
                                        array $a,
                                        array $b,
                                    ): int {
                                        if (
                                            $a['priority']
                                            !== $b['priority']
                                        ) {
                                            return
                                                $b['priority']
                                                <=> $a['priority'];
                                        }

                                        return
                                            $a['catalog_index']
                                            <=> $b['catalog_index'];
                                    },
                                )
                                ->values();

                            return [
                                'title' => (string) $area[
                                    'name'
                                ],
                                'priority' => (float) (
                                    $items->max(
                                        'priority',
                                    ) ?? 0
                                ),
                                'catalog_index' => $areaIndex,
                                'items' => $items->all(),
                            ];
                        },
                    )
                    ->sort(
                        function (
                            array $a,
                            array $b,
                        ): int {
                            if (
                                $a['priority']
                                !== $b['priority']
                            ) {
                                return
                                    $b['priority']
                                    <=> $a['priority'];
                            }

                            return
                                $a['catalog_index']
                                <=> $b['catalog_index'];
                        },
                    )
                    ->values();

                Roadmap::query()
                    ->where(
                        'user_id',
                        $user->id,
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->update([
                        'is_active' => false,
                    ]);

                $version = (
                    (int) Roadmap::query()
                        ->where(
                            'user_id',
                            $user->id,
                        )
                        ->where(
                            'career_id',
                            $user
                                ->target_career_id,
                        )
                        ->max(
                            'version',
                        )
                ) + 1;

                $totalMinutes = (int) $areas
                    ->sum(
                        fn (array $area): int => (
                            (int) collect(
                                $area['items'],
                            )->sum(
                                fn (array $item): int => (
                                    (int) $item[
                                        'material'
                                    ]->estimated_minutes
                                ),
                            )
                        ),
                    );

                $weeklyMinutes = max(
                    (
                        (int) $user
                            ->weekly_study_hours
                    ) * 60,
                    60,
                );

                $roadmap = Roadmap::create([
                    'user_id' => $user->id,
                    'career_id' => $user
                        ->target_career_id,
                    'version' => $version,
                    'reason' => $reason,
                    'estimated_weeks' => max(
                        (int) ceil(
                            $totalMinutes
                            / $weeklyMinutes,
                        ),
                        1,
                    ),
                    'is_active' => true,
                ]);

                $position = 1;

                foreach (
                    $areas as $stageIndex => $area
                ) {
                    $stage = $stageIndex + 1;

                    foreach (
                        $area['items'] as $entry
                    ) {
                        $material = $entry[
                            'material'
                        ];

                        $roadmap
                            ->items()
                            ->create([
                                'learning_material_id' => $material->id,
                                'stage' => $stage,
                                'stage_title' => $area[
                                    'title'
                                ],
                                'position' => $position++,
                                'status' => 'locked',
                                'progress_percentage' => 0,
                                'unlocked_at' => null,
                            ]);
                    }
                }

                $this->applySequentialAvailability(
                    $roadmap,
                );

                return $roadmap->fresh([
                    'items.material.skill',
                ]);
            },
        );
    }

    public function refreshAvailability(
        User $user,
    ): void {
        $roadmap = Roadmap::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->where(
                'is_active',
                true,
            )
            ->first();

        if (! $roadmap) {
            return;
        }

        $this->applySequentialAvailability(
            $roadmap,
        );
    }

    public function adaptAfterSkillChange(
        User $user,
        string $reason = 'Perubahan skor skill setelah evaluasi',
    ): ?Roadmap {
        $user->loadMissing(
            'targetCareer',
        );

        if (! $user->targetCareer) {
            return null;
        }

        return DB::transaction(
            function () use (
                $user,
                $reason,
            ) {
                $this->refreshAvailability(
                    $user,
                );

                $roadmap = Roadmap::query()
                    ->where(
                        'user_id',
                        $user->id,
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->with([
                        'items.material.skill.prerequisites',
                    ])
                    ->lockForUpdate()
                    ->first();

                if (! $roadmap) {
                    return null;
                }

                $analysis = collect(
                    $this
                        ->skillGapService
                        ->analyze($user),
                )->keyBy(
                    'skill_id',
                );

                $reinforcementParentIds = $roadmap
                    ->items
                    ->pluck(
                        'reinforcement_for_roadmap_item_id',
                    )
                    ->filter()
                    ->unique()
                    ->values();

                $changed = false;

                foreach (
                    $roadmap
                        ->items
                        ->groupBy(
                            'stage',
                        ) as $stageItems
                ) {
                    $candidates = $stageItems
                        ->filter(
                            function (
                                RoadmapItem $item,
                            ) use (
                                $reinforcementParentIds,
                            ): bool {
                                return
                                    $item
                                        ->material
                                        ->material_type
                                    !== 'reinforcement'
                                    && ! in_array(
                                        $item->status,
                                        [
                                            'completed',
                                            'reinforcement_required',
                                        ],
                                        true,
                                    )
                                    && ! $reinforcementParentIds
                                        ->contains(
                                            $item->id,
                                        );
                            },
                        )
                        ->map(
                            function (
                                RoadmapItem $item,
                            ) use (
                                $analysis,
                            ): array {
                                $skillId = (int) $item
                                    ->material
                                    ->skill_id;

                                $analysisItem = $analysis->get(
                                    $skillId,
                                );

                                return [
                                    'item' => $item,
                                    'availability_rank' => $this
                                        ->availabilityRank(
                                            $item->status,
                                        ),
                                    'priority' => is_array(
                                        $analysisItem,
                                    )
                                        ? (float) (
                                            $analysisItem[
                                                'priority'
                                            ] ?? 0
                                        )
                                        : 0.0,
                                ];
                            },
                        );

                    $slots = $candidates
                        ->map(
                            fn (array $entry): int => (
                                (int) $entry[
                                    'item'
                                ]->position
                            ),
                        )
                        ->sort()
                        ->values();

                    $ordered = $candidates
                        ->sort(
                            function (
                                array $a,
                                array $b,
                            ): int {
                                if (
                                    $a[
                                        'availability_rank'
                                    ]
                                    !== $b[
                                        'availability_rank'
                                    ]
                                ) {
                                    return
                                        $a[
                                            'availability_rank'
                                        ]
                                        <=> $b[
                                            'availability_rank'
                                        ];
                                }

                                if (
                                    $a['priority']
                                    !== $b['priority']
                                ) {
                                    return
                                        $b['priority']
                                        <=> $a['priority'];
                                }

                                return
                                    $a[
                                        'item'
                                    ]->position
                                    <=> $b[
                                        'item'
                                    ]->position;
                            },
                        )
                        ->values();

                    foreach (
                        $ordered as $index => $entry
                    ) {
                        $item = $entry[
                            'item'
                        ];

                        $position = (int) $slots[
                            $index
                        ];

                        if (
                            (int) $item
                                ->position
                            === $position
                        ) {
                            continue;
                        }

                        $item->update([
                            'position' => $position,
                        ]);

                        $changed = true;
                    }
                }

                $this->recalculateEstimatedWeeks(
                    $roadmap,
                    $user,
                );

                $this->applySequentialAvailability(
                    $roadmap,
                );

                if ($changed) {
                    ProgressLog::create([
                        'user_id' => $user->id,
                        'activity_type' => 'roadmap_reordered',
                        'minutes_spent' => 0,
                        'progress_percentage' => 0,
                        'notes' => $reason,
                        'logged_at' => now(),
                    ]);
                }

                return $roadmap
                    ->fresh([
                        'items.material.skill.prerequisites',
                    ]);
            },
        );
    }

    private function applySequentialAvailability(
        Roadmap $roadmap,
    ): void {
        $items = RoadmapItem::query()
            ->where(
                'roadmap_id',
                $roadmap->id,
            )
            ->orderBy(
                'position',
            )
            ->orderBy(
                'id',
            )
            ->get();

        $activeItemFound = false;

        foreach ($items as $item) {
            if (
                $item->status
                === 'completed'
            ) {
                continue;
            }

            if ($activeItemFound) {
                if (
                    $item->status
                    === 'reinforcement_required'
                ) {
                    continue;
                }

                if (
                    $item->status
                    !== 'locked'
                    || $item->unlocked_at
                    !== null
                ) {
                    $item->update([
                        'status' => 'locked',
                        'unlocked_at' => null,
                    ]);
                }

                continue;
            }

            $activeItemFound = true;

            if (
                $item->status
                === 'reinforcement_required'
            ) {
                continue;
            }

            $nextStatus = $item->status
                === 'needs_reinforcement'
                ? 'needs_reinforcement'
                : 'available';

            $updates = [
                'status' => $nextStatus,
            ];

            if (
                $item->unlocked_at
                === null
            ) {
                $updates[
                    'unlocked_at'
                ] = now();
            }

            if (
                $item->status
                !== $nextStatus
                || $item->unlocked_at
                === null
            ) {
                $item->update(
                    $updates,
                );
            }
        }
    }

    private function availabilityRank(
        string $status,
    ): int {
        return match ($status) {
            'available',
            'needs_reinforcement' => 0,
            'locked' => 1,
            default => 2,
        };
    }

    private function recalculateEstimatedWeeks(
        Roadmap $roadmap,
        User $user,
    ): void {
        $roadmap->loadMissing(
            'items.material',
        );

        $remainingMinutes = $roadmap
            ->items
            ->where(
                'status',
                '!=',
                'completed',
            )
            ->sum(
                fn ($item) => (
                    (int) $item
                        ->material
                        ->estimated_minutes
                ),
            );

        $weeklyMinutes = max(
            (
                (int) $user
                    ->weekly_study_hours
            ) * 60,
            60,
        );

        $roadmap->update([
            'estimated_weeks' => max(
                (int) ceil(
                    $remainingMinutes
                    / $weeklyMinutes,
                ),
                1,
            ),
        ]);
    }
}
