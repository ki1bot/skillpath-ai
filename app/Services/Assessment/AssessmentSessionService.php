<?php

namespace App\Services\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class AssessmentSessionService
{
    public function __construct(
        private readonly AssessmentSessionBuilder $builder,
        private readonly AssessmentSessionStore $store,
        private readonly AssessmentSessionValidator $validator,
    ) {}

    /**
     * @param  Collection<int, AssessmentQuestion>  $questionPool
     * @return array{
     *     question_ids: list<int>,
     *     reserve_question_ids: list<int>,
     *     has_stored_session: bool,
     *     started: bool
     * }
     */
    public function state(
        Request $request,
        Assessment $assessment,
        int $userId,
        Collection $questionPool,
    ): array {
        $stored = $this->store->read(
            $request,
            $assessment,
            $userId,
        );

        return [
            'question_ids' => $stored['question_ids'],
            'reserve_question_ids' => $stored['reserve_question_ids'],
            'has_stored_session' => $stored['has_stored_session'],
            'started' => $this->validator->isValid(
                $questionPool,
                $stored['question_ids'],
                $stored['reserve_question_ids'],
            ),
        ];
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questionPool
     * @param  list<string>  $skillSlugs
     */
    public function start(
        Request $request,
        Assessment $assessment,
        int $userId,
        Collection $questionPool,
        array $skillSlugs,
    ): bool {
        $session = $this->builder->build(
            $questionPool,
            $skillSlugs,
        );

        if (
            ! $this->validator->isValid(
                $questionPool,
                $session['question_ids'],
                $session['reserve_question_ids'],
            )
        ) {
            return false;
        }

        $this->store->write(
            $request,
            $assessment,
            $userId,
            $session['question_ids'],
            $session['reserve_question_ids'],
        );

        return true;
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questionPool
     * @param  list<int>  $questionIds
     * @return Collection<int, AssessmentQuestion>
     */
    public function questionsInStoredOrder(
        Collection $questionPool,
        array $questionIds,
    ): Collection {
        $positions = array_flip(
            $questionIds,
        );

        return $questionPool
            ->filter(
                fn (AssessmentQuestion $question) => array_key_exists(
                    $question->id,
                    $positions,
                ),
            )
            ->sortBy(
                fn (AssessmentQuestion $question) => $positions[
                    $question->id
                ],
            )
            ->values();
    }

    public function clear(
        Request $request,
        Assessment $assessment,
        int $userId,
    ): void {
        $this->store->clear(
            $request,
            $assessment,
            $userId,
        );
    }
}
