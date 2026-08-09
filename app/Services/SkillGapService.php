<?php

namespace App\Services;

use App\Models\User;

class SkillGapService
{
    public function analyze(User $user): array
    {
        $career = $user->targetCareer;

        if (! $career) {
            return [];
        }

        $scores = $user->userSkills()->pluck('score', 'skill_id');
        $skills = $career->skills()
            ->with(['prerequisites:id,name,slug', 'dependents:id,name,slug'])
            ->get();

        return $skills
            ->map(function ($skill) use ($scores) {
                $current = (float) ($scores[$skill->id] ?? 0);
                $target = (float) $skill->pivot->target_level;
                $gap = max($target - $current, 0);
                $weight = (float) $skill->pivot->importance_weight;
                $dependentCount = $skill->dependents->count();
                $baseDependencyFactor = $skill->dependents
                    ->map(fn ($dependent) => (float) $dependent->pivot->factor)
                    ->max() ?: 1.0;
                $prerequisiteFactor = $dependentCount > 0
                    ? $baseDependencyFactor + min(($dependentCount - 1) * 0.05, 0.25)
                    : 1.0;

                $priority = round($gap * $weight * $prerequisiteFactor, 2);
                $status = match (true) {
                    $gap <= 0 => 'terpenuhi',
                    $gap >= 30 => 'kesenjangan_tinggi',
                    default => 'perlu_ditingkatkan',
                };

                return [
                    'skill_id' => $skill->id,
                    'name' => $skill->name,
                    'slug' => $skill->slug,
                    'category' => $skill->category,
                    'description' => $skill->description,
                    'current' => round($current, 1),
                    'target' => round($target, 1),
                    'gap' => round($gap, 1),
                    'importance_weight' => $weight,
                    'prerequisite_factor' => round($prerequisiteFactor, 2),
                    'priority' => $priority,
                    'status' => $status,
                    'required' => (bool) $skill->pivot->is_required,
                    'prerequisites' => $skill->prerequisites->map(fn ($item) => [
                        'id' => $item->id,
                        'name' => $item->name,
                        'slug' => $item->slug,
                    ])->values()->all(),
                    'reason' => $this->reason(
                        $skill->name,
                        $current,
                        $target,
                        $gap,
                        $dependentCount,
                    ),
                ];
            })
            ->sortByDesc('priority')
            ->values()
            ->all();
    }

    public function topPriorities(User $user, int $limit = 3): array
    {
        return collect($this->analyze($user))
            ->filter(fn (array $item) => $item['gap'] > 0)
            ->take($limit)
            ->values()
            ->all();
    }

    public function averageMastery(User $user): float
    {
        $analysis = collect($this->analyze($user))
            ->where('required', true)
            ->values();

        if ($analysis->isEmpty()) {
            return 0;
        }

        $weighted = $analysis->sum(function (array $item) {
            $ratio = $item['target'] > 0 ? min($item['current'] / $item['target'], 1) : 1;

            return $ratio * 100 * $item['importance_weight'];
        });

        $weights = max($analysis->sum('importance_weight'), 1);

        return round($weighted / $weights, 1);
    }

    private function reason(
        string $skill,
        float $current,
        float $target,
        float $gap,
        int $dependentCount,
    ): string {
        if ($gap <= 0) {
            return "$skill sudah memenuhi standar target $target karena skor Anda saat ini $current.";
        }

        $dependency = $dependentCount > 0
            ? " Skill ini juga menjadi fondasi bagi $dependentCount skill lanjutan."
            : '';

        return "$skill diprioritaskan karena skor Anda $current dari target $target, sehingga masih ada gap $gap poin.$dependency";
    }
}
