<?php

namespace App\Services;

use App\Models\LearningMaterial;
use App\Models\ProgressLog;
use App\Models\Roadmap;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RoadmapService
{
    public function __construct(
        private readonly SkillGapService $skillGapService,
    ) {}

    public function regenerate(
        User $user,
        string $reason = 'Assesment awal',
    ): Roadmap {
        $user->loadMissing(
            'targetCareer',
        );

        if (! $user->targetCareer) {
            throw new \RuntimeException(
                'Target karier belum dipilih.',
            );
        }

        return DB::transaction(
            function () use (
                $user,
                $reason,
            ) {
                $analysis = collect(
                    $this
                        ->skillGapService
                        ->analyze($user),
                )
                    ->filter(
                        fn (array $item) => (
                            $item['gap'] > 0
                        ),
                    )
                    ->values();

                $skillIds = $analysis
                    ->pluck('skill_id')
                    ->all();

                $skills = Skill::query()
                    ->whereIn(
                        'id',
                        $skillIds,
                    )
                    ->with(
                        'prerequisites:id,name,slug',
                    )
                    ->get()
                    ->keyBy('id');

                $materialsBySkill = LearningMaterial::query()
                    ->whereIn(
                        'skill_id',
                        $skillIds,
                    )
                    ->where(
                        'material_type',
                        'core',
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->orderBy('id')
                    ->get()
                    ->groupBy('skill_id');

                $scores = $user
                    ->userSkills()
                    ->pluck(
                        'score',
                        'skill_id',
                    );

                $depthMemo = [];

                $ordered = $analysis
                    ->map(
                        function (
                            array $item,
                        ) use (
                            $skills,
                            &$depthMemo,
                        ) {
                            $item['depth'] = $this->depth(
                                $item['skill_id'],
                                $skills,
                                $depthMemo,
                                [],
                            );

                            return $item;
                        },
                    )
                    ->sort(
                        function (
                            array $a,
                            array $b,
                        ) {
                            if (
                                $a['depth']
                                === $b['depth']
                            ) {
                                return
                                    $b['priority']
                                    <=> $a['priority'];
                            }

                            return
                                $a['depth']
                                <=> $b['depth'];
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
                        ->max('version')
                ) + 1;

                $totalMinutes = $ordered
                    ->sum(
                        function (
                            array $item,
                        ) use (
                            $materialsBySkill,
                        ) {
                            $materials = $materialsBySkill
                                ->get(
                                    $item['skill_id'],
                                );

                            return $materials
                                ? (int) $materials
                                    ->sum(
                                        'estimated_minutes',
                                    )
                                : 0;
                        },
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
                    $ordered as $item
                ) {
                    $skill = $skills->get(
                        $item['skill_id'],
                    );

                    if (! $skill) {
                        continue;
                    }

                    $materials = $materialsBySkill
                        ->get(
                            $item['skill_id'],
                            collect(),
                        );

                    $available = $this
                        ->prerequisitesSatisfied(
                            $skill,
                            $scores,
                        );

                    $stage = min(
                        max(
                            (int) $item['depth'],
                            1,
                        ),
                        4,
                    );

                    foreach (
                        $materials as $material
                    ) {
                        $roadmap
                            ->items()
                            ->create([
                                'learning_material_id' => $material->id,
                                'stage' => $stage,
                                'stage_title' => $this->stageTitle(
                                    $stage,
                                ),
                                'position' => $position++,
                                'status' => $available
                                    ? 'available'
                                    : 'locked',
                                'progress_percentage' => 0,
                                'unlocked_at' => $available
                                    ? now()
                                    : null,
                            ]);
                    }
                }

                return $roadmap->load(
                    'items.material.skill',
                );
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
            ->with(
                'items.material.skill.prerequisites',
            )
            ->first();

        if (! $roadmap) {
            return;
        }

        $scores = $user
            ->userSkills()
            ->pluck(
                'score',
                'skill_id',
            );

        foreach (
            $roadmap->items as $item
        ) {
            if (
                in_array(
                    $item->status,
                    [
                        'completed',
                        'needs_reinforcement',
                        'reinforcement_required',
                    ],
                    true,
                )
            ) {
                continue;
            }

            $available = $this
                ->prerequisitesSatisfied(
                    $item
                        ->material
                        ->skill,
                    $scores,
                );

            $item->update([
                'status' => $available
                    ? 'available'
                    : 'locked',
                'unlocked_at' => $available
                    ? (
                        $item->unlocked_at
                        ?? now()
                    )
                    : null,
            ]);
        }
    }

    public function adaptAfterSkillChange(
        User $user,
        string $reason = 'Perubahan skor skill setelah evaluasi',
    ): ?Roadmap {
        $user->loadMissing('targetCareer');

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
                )->keyBy('skill_id');

                $skills = $roadmap
                    ->items
                    ->map(
                        fn ($item) => $item
                            ->material
                            ?->skill,
                    )
                    ->filter()
                    ->unique('id')
                    ->keyBy('id');

                $reinforcementParentIds = $roadmap
                    ->items
                    ->pluck(
                        'reinforcement_for_roadmap_item_id',
                    )
                    ->filter()
                    ->unique()
                    ->values();

                $depthMemo = [];

                $candidates = $roadmap
                    ->items
                    ->filter(
                        function ($item) use (
                            $reinforcementParentIds,
                        ) {
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
                        function ($item) use (
                            $analysis,
                            $skills,
                            &$depthMemo,
                        ) {
                            $skillId = $item
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
                                'depth' => $this->depth(
                                    $skillId,
                                    $skills,
                                    $depthMemo,
                                    [],
                                ),
                                'priority' => is_array(
                                    $analysisItem,
                                )
                                    ? (float) $analysisItem[
                                        'priority'
                                    ]
                                    : 0.0,
                            ];
                        },
                    );

                $slots = $candidates
                    ->map(
                        fn (array $entry) => (
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
                        ) {
                            if (
                                $a['availability_rank']
                                !== $b['availability_rank']
                            ) {
                                return
                                    $a['availability_rank']
                                    <=> $b['availability_rank'];
                            }

                            if (
                                $a['depth']
                                !== $b['depth']
                            ) {
                                return
                                    $a['depth']
                                    <=> $b['depth'];
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
                                $a['item']->position
                                <=> $b['item']->position;
                        },
                    )
                    ->values();

                $changed = false;

                foreach (
                    $ordered as $index => $entry
                ) {
                    $item = $entry['item'];
                    $position = (int) $slots[$index];

                    $stage = min(
                        max(
                            (int) $entry['depth'],
                            1,
                        ),
                        4,
                    );

                    if (
                        (int) $item->position !== $position
                        || (int) $item->stage !== $stage
                        || $item->stage_title
                            !== $this->stageTitle($stage)
                    ) {
                        $changed = true;
                    }

                    $item->update([
                        'position' => $position,
                        'stage' => $stage,
                        'stage_title' => $this->stageTitle(
                            $stage,
                        ),
                    ]);
                }

                $this->recalculateEstimatedWeeks(
                    $roadmap,
                    $user,
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

    private function depth(
        int $skillId,
        Collection $skills,
        array &$memo,
        array $trail,
    ): int {
        if (
            isset(
                $memo[$skillId],
            )
        ) {
            return $memo[$skillId];
        }

        if (
            in_array(
                $skillId,
                $trail,
                true,
            )
        ) {
            return 1;
        }

        $skill = $skills->get(
            $skillId,
        );

        if (! $skill) {
            return 1;
        }

        $trail[] = $skillId;

        $prerequisiteIds = $skill
            ->prerequisites
            ->pluck('id')
            ->filter(
                fn ($id) => (
                    $skills->has($id)
                ),
            );

        if (
            $prerequisiteIds->isEmpty()
        ) {
            return $memo[$skillId] = 1;
        }

        $depth = 1
            + $prerequisiteIds->max(
                fn ($id) => $this->depth(
                    (int) $id,
                    $skills,
                    $memo,
                    $trail,
                ),
            );

        return $memo[$skillId] = $depth;
    }

    private function prerequisitesSatisfied(
        Skill $skill,
        Collection $scores,
    ): bool {
        if (
            $skill
                ->prerequisites
                ->isEmpty()
        ) {
            return true;
        }

        return $skill
            ->prerequisites
            ->every(
                function (
                    $prerequisite,
                ) use (
                    $scores,
                ) {
                    return
                        (float) (
                            $scores[
                                $prerequisite->id
                            ]
                            ?? 0
                        )
                        >= 60;
                },
            );
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

    private function stageTitle(
        int $stage,
    ): string {
        return match ($stage) {
            1 => 'Fondasi',
            2 => 'Penguatan Inti',
            3 => 'Penerapan',
            default => 'Quality & Delivery',
        };
    }
}
