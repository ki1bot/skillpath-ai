<?php

namespace App\Services;

use App\Models\ReadinessSnapshot;
use App\Models\Roadmap;
use App\Models\User;

class CareerReadinessService
{
    public function __construct(private readonly SkillGapService $skillGapService)
    {
    }

    public function snapshot(User $user, string $trigger): ReadinessSnapshot
    {
        $readiness = $this->calculate($user);

        return ReadinessSnapshot::create([
            'user_id' => $user->id,
            'career_id' => $user->target_career_id,
            'trigger' => $trigger,
            'score' => $readiness['score'],
            'skill_mastery' => $readiness['skill_mastery'],
            'roadmap_completion' => $readiness['roadmap_completion'],
            'project_score' => $readiness['project_score'],
            'consistency' => $readiness['consistency'],
            'evaluation_score' => $readiness['evaluation_score'],
        ]);
    }

    public function calculate(User $user): array
    {
        $skillMastery = $this->skillGapService->averageMastery($user);
        $roadmap = Roadmap::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->withCount([
                'items',
                'items as completed_items_count' => fn ($query) => $query->where('status', 'completed'),
            ])
            ->first();

        $roadmapCompletion = $roadmap && $roadmap->items_count > 0
            ? round(($roadmap->completed_items_count / $roadmap->items_count) * 100, 1)
            : 0;

        $projects = $user->projects()
            ->whereHas('project', fn ($query) => $query->where('career_id', $user->target_career_id))
            ->get();

        $projectScore = $projects->isEmpty()
            ? 0
            : round((float) $projects->max('progress_percentage'), 1);

        $activeDays = $user->progressLogs()
            ->where('logged_at', '>=', now()->subDays(28))
            ->get()
            ->pluck('logged_at')
            ->filter()
            ->map(fn ($date) => $date->toDateString())
            ->unique()
            ->count();

        $consistency = round(min(($activeDays / 12) * 100, 100), 1);

        $evaluationScore = round((float) $user->evaluations()
            ->latest()
            ->limit(5)
            ->get()
            ->avg('score'), 1);

        $score = round(
            ($skillMastery * 0.45)
            + ($roadmapCompletion * 0.20)
            + ($projectScore * 0.20)
            + ($consistency * 0.10)
            + ($evaluationScore * 0.05),
            1,
        );

        return [
            'score' => $score,
            'skill_mastery' => $skillMastery,
            'roadmap_completion' => $roadmapCompletion,
            'project_score' => $projectScore,
            'consistency' => $consistency,
            'evaluation_score' => $evaluationScore,
            'active_days_28' => $activeDays,
        ];
    }
}
