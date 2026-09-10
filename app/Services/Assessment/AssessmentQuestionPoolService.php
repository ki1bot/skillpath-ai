<?php

namespace App\Services\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Database\Eloquent\Collection;

class AssessmentQuestionPoolService
{
    /**
     * @param  list<string>  $skillSlugs
     * @return Collection<int, AssessmentQuestion>
     */
    public function load(
        Assessment $assessment,
        array $skillSlugs,
    ): Collection {
        return $assessment
            ->questions()
            ->whereHas(
                'skill',
                fn ($query) => $query->whereIn(
                    'slug',
                    $skillSlugs,
                ),
            )
            ->with('skill')
            ->get();
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questions
     * @param  list<string>  $skillSlugs
     */
    public function isValid(
        Collection $questions,
        string $studyProgram,
        array $skillSlugs,
    ): bool {
        if (
            $questions->count()
            !== AcademicAssessmentCatalog::QUESTION_POOL_SIZE
        ) {
            return false;
        }

        if (
            $questions
                ->pluck('skill.slug')
                ->filter()
                ->unique()
                ->count()
            !== AcademicAssessmentCatalog::SKILLS_PER_PROGRAM
        ) {
            return false;
        }

        foreach ($skillSlugs as $skillSlug) {
            $questionCount = $questions
                ->filter(
                    fn (AssessmentQuestion $question) => $question
                        ->skill
                        ?->slug === $skillSlug,
                )
                ->count();

            $expectedCount = AcademicAssessmentCatalog::questionCapacityForSkill(
                $studyProgram,
                $skillSlug,
            );

            if ($questionCount !== $expectedCount) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questions
     * @return list<array{
     *     id: int,
     *     question_type: string,
     *     prompt: string,
     *     practical_instructions: mixed,
     *     evidence_required: bool,
     *     options: mixed,
     *     difficulty: string,
     *     skill: array{id: int, name: string, category: string}
     * }>
     */
    public function toPayload(Collection $questions): array
    {
        $payload = [];

        foreach ($questions as $question) {
            $skill = $question->skill;

            if (! $skill) {
                continue;
            }

            $payload[] = [
                'id' => (int) $question->id,
                'question_type' => (string) $question->question_type,
                'prompt' => (string) $question->prompt,
                'practical_instructions' => $question->practical_instructions,
                'evidence_required' => (bool) $question->evidence_required,
                'options' => $question->options,
                'difficulty' => (string) $question->difficulty,
                'skill' => [
                    'id' => (int) $skill->id,
                    'name' => (string) $skill->name,
                    'category' => (string) $skill->category,
                ],
            ];
        }

        return $payload;
    }
}
