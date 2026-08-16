<?php

namespace App\Services;

use App\Models\PortfolioProject;
use App\Models\User;

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

        if ($missing->isEmpty()) {
            $recommendation = [
                'level' => 'recommended',
                'rank' => 0,
                'label' => 'Direkomendasikan sekarang',
                'message' => 'Seluruh prasyarat minimum proyek sudah terpenuhi. Proyek ini paling sesuai untuk dikerjakan pada kondisi kemampuan Anda saat ini.',
            ];
        } else {
            $missingRatio = $requirements->count() > 0
                ? $missing->count() / $requirements->count()
                : 0;

            $gapText = $topGapNames === []
                ? 'beberapa skill prasyarat'
                : implode(', ', $topGapNames);

            if (
                $score >= 70
                && $missingRatio <= 0.4
            ) {
                $recommendation = [
                    'level' => 'strengthen',
                    'rank' => 1,
                    'label' => 'Perlu penguatan',
                    'message' => "Kesiapan Anda sudah cukup dekat, tetapi {$gapText} masih perlu diperkuat sebelum proyek ini menjadi rekomendasi utama.",
                ];
            } else {
                $recommendation = [
                    'level' => 'challenge',
                    'rank' => 2,
                    'label' => 'Challenge',
                    'message' => "Gap kemampuan untuk proyek ini masih cukup besar, terutama pada {$gapText}. Anda tetap boleh memulai sebagai challenge, tetapi waktu pengerjaan dan risiko hambatannya lebih tinggi.",
                ];
            }
        }

        return [
            'score' => $score,
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
