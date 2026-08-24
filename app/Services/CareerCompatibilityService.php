<?php

namespace App\Services;

use App\Models\Career;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Support\Collection;

class CareerCompatibilityService
{
    /**
     * @return array<string, mixed>
     */
    public function calculate(
        User $user,
        Career $career,
    ): array {
        $scores = $this->userScores(
            $user,
        );

        return $this->calculateWithScores(
            $career,
            $scores,
        );
    }

    /**
     * @param  Collection<int, Career>  $careers
     * @return array<int, array<string, mixed>>
     */
    public function calculateForCareers(
        User $user,
        Collection $careers,
    ): array {
        $scores = $this->userScores(
            $user,
        );

        return $careers
            ->mapWithKeys(
                fn (Career $career): array => [
                    $career->id => $this
                        ->calculateWithScores(
                            $career,
                            $scores,
                        ),
                ],
            )
            ->all();
    }

    /**
     * @return Collection<int<0, max>, float>
     */
    private function userScores(
        User $user,
    ): Collection {
        return UserSkill::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->get([
                'skill_id',
                'score',
            ])
            ->mapWithKeys(
                fn (UserSkill $userSkill): array => [
                    $userSkill->skill_id => (float) $userSkill
                        ->score,
                ],
            );
    }

    /**
     * @param  Collection<int<0, max>, float>  $scores
     * @return array<string, mixed>
     */
    private function calculateWithScores(
        Career $career,
        Collection $scores,
    ): array {
        $career->loadMissing('skills');

        $skills = $career->skills;

        if ($skills->isEmpty()) {
            return [
                'score' => 0,
                'label' => 'Belum tersedia',
                'assessed_skills' => 0,
                'total_skills' => 0,
                'top_gaps' => [],
            ];
        }

        $weightedScore = 0.0;
        $totalWeight = 0.0;
        $assessedSkills = 0;

        $gaps = $skills
            ->map(
                function (
                    Skill $skill,
                ) use (
                    $scores,
                    &$weightedScore,
                    &$totalWeight,
                    &$assessedSkills,
                ): array {
                    $pivot = $skill->pivot;

                    $target = (float) (
                        $pivot?->getAttribute(
                            'target_level',
                        )
                        ?? 0
                    );

                    $importance = (float) (
                        $pivot?->getAttribute(
                            'importance_weight',
                        )
                        ?? 1
                    );

                    $importance = max(
                        0.01,
                        $importance,
                    );

                    $current = (float) $scores
                        ->get(
                            $skill->id,
                            0.0,
                        );

                    if (
                        $scores->has(
                            $skill->id,
                        )
                    ) {
                        $assessedSkills++;
                    }

                    $ratio = $target > 0
                        ? min(
                            $current / $target,
                            1,
                        )
                        : 1;

                    $weightedScore += (
                        $ratio
                        * $importance
                    );

                    $totalWeight += $importance;

                    $gap = max(
                        $target - $current,
                        0,
                    );

                    return [
                        'skill_id' => $skill->id,
                        'name' => $skill->name,
                        'current' => round(
                            $current,
                            2,
                        ),
                        'target' => round(
                            $target,
                            2,
                        ),
                        'gap' => round(
                            $gap,
                            2,
                        ),
                        'importance' => round(
                            $importance,
                            2,
                        ),
                        'priority' => round(
                            $gap
                            * $importance,
                            2,
                        ),
                    ];
                },
            );

        $score = $totalWeight > 0
            ? round(
                (
                    $weightedScore
                    / $totalWeight
                ) * 100,
                2,
            )
            : 0;

        $topGaps = $gaps
            ->filter(
                fn (array $gap): bool => (
                    (float) $gap['gap']
                ) > 0,
            )
            ->sortByDesc(
                'priority',
            )
            ->take(3)
            ->values()
            ->all();

        return [
            'score' => $score,
            'label' => $this->label(
                $score,
            ),
            'assessed_skills' => $assessedSkills,
            'total_skills' => $skills->count(),
            'top_gaps' => $topGaps,
        ];
    }

    private function label(
        float $score,
    ): string {
        if ($score >= 85) {
            return 'Sangat siap';
        }

        if ($score >= 70) {
            return 'Siap';
        }

        if ($score >= 50) {
            return 'Cukup siap';
        }

        if ($score > 0) {
            return 'Perlu penguatan';
        }

        return 'Belum dinilai';
    }
}
