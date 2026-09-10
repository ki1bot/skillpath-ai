<?php

namespace App\Services\Assessment;

use App\Models\Assessment;
use Illuminate\Http\Request;

class AssessmentSessionStore
{
    /**
     * @return array{
     *     question_ids: list<int>,
     *     has_stored_session: bool
     * }
     */
    public function read(
        Request $request,
        Assessment $assessment,
        int $userId,
    ): array {
        $questionIds = $this->normalizeQuestionIds(
            $request
                ->session()
                ->get(
                    $this->questionSessionKey(
                        $assessment->id,
                        $userId,
                    ),
                ),
        );

        return [
            'question_ids' => $questionIds,
            'has_stored_session' => $questionIds !== [],
        ];
    }

    /**
     * @param  list<int>  $questionIds
     */
    public function write(
        Request $request,
        Assessment $assessment,
        int $userId,
        array $questionIds,
    ): void {
        $request
            ->session()
            ->forget(
                $this->legacyReserveQuestionSessionKey(
                    $assessment->id,
                    $userId,
                ),
            );

        $request
            ->session()
            ->put(
                $this->questionSessionKey(
                    $assessment->id,
                    $userId,
                ),
                $questionIds,
            );
    }

    public function clear(
        Request $request,
        Assessment $assessment,
        int $userId,
    ): void {
        $request
            ->session()
            ->forget([
                $this->questionSessionKey(
                    $assessment->id,
                    $userId,
                ),
                $this->legacyReserveQuestionSessionKey(
                    $assessment->id,
                    $userId,
                ),
            ]);
    }

    /**
     * @return list<int>
     */
    private function normalizeQuestionIds(
        mixed $value,
    ): array {
        if (! is_array($value)) {
            return [];
        }

        $questionIds = [];
        $seen = [];

        foreach ($value as $id) {
            $questionId = (int) $id;

            if (
                $questionId <= 0
                || isset($seen[$questionId])
            ) {
                continue;
            }

            $seen[$questionId] = true;
            $questionIds[] = $questionId;
        }

        return $questionIds;
    }

    private function questionSessionKey(
        int $assessmentId,
        int $userId,
    ): string {
        return 'assessment.question_ids.'
            .$assessmentId
            .'.'
            .$userId;
    }

    private function legacyReserveQuestionSessionKey(
        int $assessmentId,
        int $userId,
    ): string {
        return 'assessment.reserve_question_ids.'
            .$assessmentId
            .'.'
            .$userId;
    }
}
