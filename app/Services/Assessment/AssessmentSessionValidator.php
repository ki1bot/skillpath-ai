<?php

namespace App\Services\Assessment;

use App\Models\AssessmentQuestion;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Database\Eloquent\Collection;

class AssessmentSessionValidator
{
    /**
     * @param  Collection<int, AssessmentQuestion>  $questionPool
     * @param  list<int>  $questionIds
     * @param  list<int>  $reserveQuestionIds
     */
    public function isValid(
        Collection $questionPool,
        array $questionIds,
        array $reserveQuestionIds,
    ): bool {
        if (
            count($questionIds)
            !== AcademicAssessmentCatalog::QUESTION_LIMIT
        ) {
            return false;
        }

        if (
            count($reserveQuestionIds)
            !== AcademicAssessmentCatalog::RESERVE_QUESTION_LIMIT
        ) {
            return false;
        }

        if (
            array_intersect(
                $questionIds,
                $reserveQuestionIds,
            ) !== []
        ) {
            return false;
        }

        $storedIds = array_merge(
            $questionIds,
            $reserveQuestionIds,
        );

        if (
            count(
                array_unique(
                    $storedIds,
                ),
            )
            !== AcademicAssessmentCatalog::QUESTION_POOL_SIZE
        ) {
            return false;
        }

        $poolIds = [];

        foreach ($questionPool as $question) {
            $poolIds[] = (int) $question->id;
        }

        sort(
            $storedIds,
        );

        sort(
            $poolIds,
        );

        if ($storedIds !== $poolIds) {
            return false;
        }

        return $this->hasBalancedSkillDistribution(
            $questionPool,
            $questionIds,
        );
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questionPool
     * @param  list<int>  $questionIds
     */
    private function hasBalancedSkillDistribution(
        Collection $questionPool,
        array $questionIds,
    ): bool {
        $questionLookup = array_fill_keys(
            $questionIds,
            true,
        );

        $skillCounts = [];

        foreach ($questionPool as $question) {
            if (! isset($questionLookup[$question->id])) {
                continue;
            }

            $skillId = (int) $question->skill_id;

            $skillCounts[$skillId] = (
                $skillCounts[$skillId]
                ?? 0
            ) + 1;
        }

        sort(
            $skillCounts,
        );

        return $skillCounts === [
            2,
            2,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
        ];
    }
}
