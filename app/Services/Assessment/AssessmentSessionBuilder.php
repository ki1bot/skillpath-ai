<?php

namespace App\Services\Assessment;

use App\Models\AssessmentQuestion;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Database\Eloquent\Collection;

class AssessmentSessionBuilder
{
    /**
     * @param  Collection<int, AssessmentQuestion>  $questionPool
     * @param  list<string>  $skillSlugs
     * @return array{
     *     question_ids: list<int>,
     *     reserve_question_ids: list<int>
     * }
     */
    public function build(
        Collection $questionPool,
        array $skillSlugs,
    ): array {
        $reducedSkillSlugs = $skillSlugs;

        shuffle(
            $reducedSkillSlugs,
        );

        $reducedSkillSlugs = array_slice(
            $reducedSkillSlugs,
            0,
            AcademicAssessmentCatalog::REDUCED_SKILLS_PER_SESSION,
        );

        $questionIds = [];

        foreach ($skillSlugs as $skillSlug) {
            $questionLimit = in_array(
                $skillSlug,
                $reducedSkillSlugs,
                true,
            )
                ? AcademicAssessmentCatalog::BASE_QUESTIONS_PER_SKILL - 1
                : AcademicAssessmentCatalog::BASE_QUESTIONS_PER_SKILL;

            $skillQuestionIds = $this->skillQuestionIds(
                $questionPool,
                $skillSlug,
            );

            shuffle(
                $skillQuestionIds,
            );

            foreach (
                array_slice(
                    $skillQuestionIds,
                    0,
                    $questionLimit,
                ) as $questionId
            ) {
                $questionIds[] = $questionId;
            }
        }

        shuffle(
            $questionIds,
        );

        $questionLookup = array_fill_keys(
            $questionIds,
            true,
        );

        $reserveQuestionIds = [];

        foreach ($questionPool as $question) {
            $questionId = (int) $question->id;

            if (isset($questionLookup[$questionId])) {
                continue;
            }

            $reserveQuestionIds[] = $questionId;
        }

        shuffle(
            $reserveQuestionIds,
        );

        return [
            'question_ids' => $questionIds,
            'reserve_question_ids' => array_slice(
                $reserveQuestionIds,
                0,
                AcademicAssessmentCatalog::RESERVE_QUESTION_LIMIT,
            ),
        ];
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questionPool
     * @return list<int>
     */
    private function skillQuestionIds(
        Collection $questionPool,
        string $skillSlug,
    ): array {
        $questionIds = [];

        foreach ($questionPool as $question) {
            if (
                $question->skill?->slug
                !== $skillSlug
            ) {
                continue;
            }

            $questionIds[] = (int) $question->id;
        }

        return $questionIds;
    }
}
