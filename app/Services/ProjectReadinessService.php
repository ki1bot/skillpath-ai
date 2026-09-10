<?php

namespace App\Services;

use App\Models\PortfolioProject;
use App\Models\User;
use App\Support\SkillPathScoringPolicy;

class ProjectReadinessService
{
    public function calculate(User $user, PortfolioProject $project): array
    {
        $project->loadMissing('skills');

        $scores = $user
            ->userSkills()
            ->pluck('score', 'skill_id');

        $requirements = $project
            ->skills
            ->map(
                function ($skill) use ($scores) {
                    $current = (float) (
                        $scores[$skill->id]
                        ?? 0
                    );

                    $pivot = $skill->pivot;

                    $required = (float) $pivot
                        ->required_level;

                    $weight = max(
                        (float) $pivot
                            ->weight,
                        0.1,
                    );

                    $percentage = $required > 0
                        ? min(
                            ($current / $required) * 100,
                            100,
                        )
                        : 100;

                    return [
                        'skill_id' => $skill->id,
                        'name' => $skill->name,
                        'current' => round(
                            $current,
                            1,
                        ),
                        'required' => round(
                            $required,
                            1,
                        ),
                        'gap' => round(
                            max(
                                $required - $current,
                                0,
                            ),
                            1,
                        ),
                        'weight' => round(
                            $weight,
                            2,
                        ),
                        'ready' => $current >= $required,
                        'percentage' => round(
                            $percentage,
                            1,
                        ),
                    ];
                },
            )
            ->values();

        $totalWeight = (float) $requirements
            ->sum('weight');

        $weightedScore = (float) $requirements
            ->sum(
                fn (array $item) => (
                    $item['percentage']
                    * $item['weight']
                ),
            );

        $score = $requirements->isEmpty()
            ? 100
            : round(
                $weightedScore
                / max($totalWeight, 0.1),
                1,
            );

        $missing = $requirements
            ->where('ready', false)
            ->sortByDesc(
                fn (array $item) => (
                    $item['gap']
                    * $item['weight']
                ),
            )
            ->values();

        $ready = $missing->isEmpty();

        $topGapNames = $missing
            ->take(3)
            ->pluck('name')
            ->all();

        $hasFoundationalCoverage = $requirements
            ->every(
                fn (array $item) => (
                    $item['current']
                    >= SkillPathScoringPolicy::EMERGING_LEVEL
                ),
            );

        if ($missing->isEmpty()) {
            $recommendation = [
                'level' => 'recommended',
                'rank' => 0,
                'label' => 'Bisa dikerjakan sekarang',
                'message' => 'Semua kemampuan minimum yang dibutuhkan sudah terpenuhi. Proyek ini cocok dikerjakan dengan kemampuanmu saat ini.',
            ];
        } else {
            $gapText = $topGapNames === []
                ? 'beberapa kemampuan dasar'
                : implode(', ', $topGapNames);

            if ($hasFoundationalCoverage) {
                $recommendation = [
                    'level' => 'strengthen',
                    'rank' => 1,
                    'label' => 'Perlu penguatan',
                    'message' => "Kamu sudah menunjukkan kemampuan awal pada seluruh prasyarat proyek, tetapi {$gapText} masih berada di bawah level minimum proyek dan perlu diperkuat.",
                ];
            } else {
                $recommendation = [
                    'level' => 'challenge',
                    'rank' => 2,
                    'label' => 'Tantangan',
                    'message' => "Masih ada prasyarat yang belum menunjukkan kemampuan awal yang cukup, terutama pada {$gapText}. Proyek tetap dapat diambil sebagai tantangan, tetapi risiko hambatan pengerjaan lebih tinggi.",
                ];
            }
        }

        return [
            'score' => $score,
            'score_type' => 'readiness',
            'quality_assessed' => false,
            'ready' => $ready,
            'missing_count' => $missing->count(),
            'requirements' => $requirements->all(),
            'top_gaps' => $missing
                ->take(3)
                ->all(),
            'recommendation' => $recommendation,
        ];
    }
}
