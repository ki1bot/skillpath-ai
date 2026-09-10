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
     */
    public function isValid(
        Collection $questionPool,
        array $questionIds,
    ): bool {
        if (
            count($questionIds)
            !== AcademicAssessmentCatalog::QUESTION_LIMIT
        ) {
            return false;
        }

        if (
            count(
                array_unique(
                    $questionIds,
                ),
            )
            !== AcademicAssessmentCatalog::QUESTION_LIMIT
        ) {
            return false;
        }

        if (
            $questionPool->count()
            !== AcademicAssessmentCatalog::QUESTION_POOL_SIZE
        ) {
            return false;
        }

        $poolIds = [];

        foreach ($questionPool as $question) {
            $poolIds[] = (int) $question->id;
        }

        $sessionIds = $questionIds;

        sort(
            $poolIds,
        );

        sort(
            $sessionIds,
        );

        return $sessionIds === $poolIds;
    }
}
