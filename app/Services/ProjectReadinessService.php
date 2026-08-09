<?php

namespace App\Services;

use App\Models\PortfolioProject;
use App\Models\User;

class ProjectReadinessService
{
    public function calculate(User $user, PortfolioProject $project): array
    {
        $project->loadMissing('skills');
        $scores = $user->userSkills()->pluck('score', 'skill_id');

        $requirements = $project->skills->map(function ($skill) use ($scores) {
            $current = (float) ($scores[$skill->id] ?? 0);
            $required = (float) $skill->pivot->required_level;
            $percentage = $required > 0
                ? min(($current / $required) * 100, 100)
                : 100;

            return [
                'skill_id' => $skill->id,
                'name' => $skill->name,
                'current' => round($current, 1),
                'required' => round($required, 1),
                'ready' => $current >= $required,
                'percentage' => round($percentage, 1),
            ];
        })->values();

        $score = $requirements->isEmpty()
            ? 100
            : round((float) $requirements->avg('percentage'), 1);

        return [
            'score' => $score,
            'ready' => $requirements->every(fn (array $item) => $item['ready']),
            'requirements' => $requirements->all(),
        ];
    }
}
