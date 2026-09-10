<?php

namespace App\Services\Assessment;

use App\Models\AssessmentQuestion;
use Illuminate\Database\Eloquent\Collection;

class AssessmentSessionBuilder
{
    /**
     * @param  Collection<int, AssessmentQuestion>  $questionPool
     * @return list<int>
     */
    public function build(
        Collection $questionPool,
    ): array {
        $questionIds = [];

        foreach ($questionPool as $question) {
            $questionIds[] = (int) $question->id;
        }

        shuffle(
            $questionIds,
        );

        return $questionIds;
    }
}
