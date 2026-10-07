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
                        $scores[$skill->id] ?? 0
                    );

                    $pivot = $skill->pivot;

                    $required = (float) $pivot->getAttribute(
                        'required_level',
                    );

                    $weight = max(
                        (float) $pivot->getAttribute(
                            'weight',
                        ),
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

        if ($requirements->isEmpty()) {
            return [
                'score' => 0.0,
                'score_type' => 'readiness',
                'quality_assessed' => false,
                'ready' => false,
                'missing_count' => 0,
                'requirements' => [],
                'top_gaps' => [],
                'recommendation' => [
                    'level' => 'challenge',
                    'rank' => 2,
                    'label' => 'Belum dikonfigurasi',
                    'message' => 'Proyek ini belum mempunyai daftar kemampuan yang harus dipenuhi. Hubungi admin agar persyaratan proyek diperiksa sebelum mulai mengerjakan.',
                ],
            ];
        }

        $totalWeight = (float) $requirements
            ->sum('weight');

        $weightedScore = (float) $requirements
            ->sum(
                fn (array $item) => (
                    $item['percentage']
                    * $item['weight']
                ),
            );

        $score = round(
            $weightedScore / max($totalWeight, 0.1),
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
                ? 'beberapa kemampuan'
                : implode(', ', $topGapNames);

            if ($hasFoundationalCoverage) {
                $recommendation = [
                    'level' => 'strengthen',
                    'rank' => 1,
                    'label' => 'Perlu penguatan',
                    'message' => "Kamu sudah mempunyai kemampuan awal untuk mengerjakan proyek ini. Namun, {$gapText} masih perlu ditingkatkan agar memenuhi persyaratan yang ditentukan.",
                ];
            } else {
                $recommendation = [
                    'level' => 'challenge',
                    'rank' => 2,
                    'label' => 'Tantangan',
                    'message' => "Beberapa kemampuan yang dibutuhkan belum cukup kuat, terutama {$gapText}. Proyek tetap dapat dicoba sebagai tantangan, tetapi kamu mungkin perlu mempelajari materi tambahan selama pengerjaan.",
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
