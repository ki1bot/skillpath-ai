<?php

namespace App\Services;

use App\Models\LearningMaterial;
use App\Models\Roadmap;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RoadmapService
{
    public function __construct(private readonly SkillGapService $skillGapService) {}

    public function regenerate(User $user, string $reason = 'Assesment awal'): Roadmap
    {
        $user->loadMissing('targetCareer');

        if (! $user->targetCareer) {
            throw new \RuntimeException('Target karier belum dipilih.');
        }

        return DB::transaction(function () use ($user, $reason) {
            $analysis = collect($this->skillGapService->analyze($user))
                ->filter(fn (array $item) => $item['gap'] > 0)
                ->values();

            $skillIds = $analysis->pluck('skill_id')->all();
            $skills = Skill::query()
                ->whereIn('id', $skillIds)
                ->with('prerequisites:id,name,slug')
                ->get()
                ->keyBy('id');
            $materials = LearningMaterial::query()
                ->whereIn('skill_id', $skillIds)
                ->get()
                ->keyBy('skill_id');
            $scores = $user->userSkills()->pluck('score', 'skill_id');
            $depthMemo = [];

            $ordered = $analysis
                ->map(function (array $item) use ($skills, &$depthMemo) {
                    $item['depth'] = $this->depth($item['skill_id'], $skills, $depthMemo, []);

                    return $item;
                })
                ->sort(function (array $a, array $b) {
                    if ($a['depth'] === $b['depth']) {
                        return $b['priority'] <=> $a['priority'];
                    }

                    return $a['depth'] <=> $b['depth'];
                })
                ->values();

            Roadmap::query()
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $version = ((int) Roadmap::query()
                ->where('user_id', $user->id)
                ->where('career_id', $user->target_career_id)
                ->max('version')) + 1;

            $totalMinutes = $ordered->sum(function (array $item) use ($materials) {
                return (int) optional($materials->get($item['skill_id']))->estimated_minutes;
            });
            $weeklyMinutes = max(((int) $user->weekly_study_hours) * 60, 60);

            $roadmap = Roadmap::create([
                'user_id' => $user->id,
                'career_id' => $user->target_career_id,
                'version' => $version,
                'reason' => $reason,
                'estimated_weeks' => max((int) ceil($totalMinutes / $weeklyMinutes), 1),
                'is_active' => true,
            ]);

            $position = 1;

            foreach ($ordered as $item) {
                $material = $materials->get($item['skill_id']);

                if (! $material) {
                    continue;
                }

                $skill = $skills->get($item['skill_id']);
                $available = $this->prerequisitesSatisfied($skill, $scores);
                $stage = min(max((int) $item['depth'], 1), 4);

                $roadmap->items()->create([
                    'learning_material_id' => $material->id,
                    'stage' => $stage,
                    'stage_title' => $this->stageTitle($stage),
                    'position' => $position++,
                    'status' => $available ? 'available' : 'locked',
                    'progress_percentage' => 0,
                    'unlocked_at' => $available ? now() : null,
                ]);
            }

            return $roadmap->load('items.material.skill');
        });
    }

    public function refreshAvailability(User $user): void
    {
        $roadmap = Roadmap::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->with('items.material.skill.prerequisites')
            ->first();

        if (! $roadmap) {
            return;
        }

        $scores = $user->userSkills()->pluck('score', 'skill_id');

        foreach ($roadmap->items as $item) {
            if ($item->status === 'completed' || $item->status === 'needs_reinforcement') {
                continue;
            }

            $available = $this->prerequisitesSatisfied($item->material->skill, $scores);

            $item->update([
                'status' => $available ? 'available' : 'locked',
                'unlocked_at' => $available ? ($item->unlocked_at ?? now()) : null,
            ]);
        }
    }

    private function depth(int $skillId, Collection $skills, array &$memo, array $trail): int
    {
        if (isset($memo[$skillId])) {
            return $memo[$skillId];
        }

        if (in_array($skillId, $trail, true)) {
            return 1;
        }

        $skill = $skills->get($skillId);

        if (! $skill) {
            return 1;
        }

        $trail[] = $skillId;
        $prerequisiteIds = $skill->prerequisites
            ->pluck('id')
            ->filter(fn ($id) => $skills->has($id));

        if ($prerequisiteIds->isEmpty()) {
            return $memo[$skillId] = 1;
        }

        $depth = 1 + $prerequisiteIds->max(
            fn ($id) => $this->depth((int) $id, $skills, $memo, $trail),
        );

        return $memo[$skillId] = $depth;
    }

    private function prerequisitesSatisfied(Skill $skill, Collection $scores): bool
    {
        if ($skill->prerequisites->isEmpty()) {
            return true;
        }

        return $skill->prerequisites->every(function ($prerequisite) use ($scores) {
            return (float) ($scores[$prerequisite->id] ?? 0) >= 60;
        });
    }

    private function stageTitle(int $stage): string
    {
        return match ($stage) {
            1 => 'Fondasi',
            2 => 'Penguatan Inti',
            3 => 'Penerapan',
            default => 'Quality & Delivery',
        };
    }
}
