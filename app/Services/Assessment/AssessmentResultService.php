<?php

namespace App\Services\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\User;
use App\Models\UserSkill;
use App\Support\AcademicAssessmentCatalog;
use App\Support\SkillPathScoringPolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AssessmentResultService
{
    /**
     * @param  Collection<int, AssessmentQuestion>  $questions
     * @param  list<int>  $questionIds
     * @return array<int, string>
     */
    public function validatedAnswers(
        Request $request,
        Collection $questions,
        array $questionIds,
    ): array {
        $validated = $request->validate([
            'answers' => [
                'required',
                'array',
            ],
            'answers.*' => [
                'required',
                'string',
                'in:A,B,C,D',
            ],
        ]);

        $rawAnswers = $validated['answers'] ?? null;

        if (! is_array($rawAnswers)) {
            throw ValidationException::withMessages([
                'answers' => 'Jawaban Assesment tidak valid.',
            ]);
        }

        $answers = [];

        foreach ($rawAnswers as $questionId => $answer) {
            $answers[(int) $questionId] = (string) $answer;
        }

        $expectedIds = $questionIds;

        $answerIds = array_keys(
            $answers,
        );

        sort(
            $expectedIds,
        );

        sort(
            $answerIds,
        );

        if ($answerIds !== $expectedIds) {
            throw ValidationException::withMessages([
                'answers' => 'Jawab tepat '
                    .AcademicAssessmentCatalog::QUESTION_LIMIT
                    .' pertanyaan yang diberikan pada sesi Assesment ini.',
            ]);
        }

        foreach ($questions as $question) {
            if (
                ! array_key_exists(
                    $question->id,
                    $answers,
                )
            ) {
                throw ValidationException::withMessages([
                    'answers' => 'Jawab semua pertanyaan sebelum menyelesaikan Assesment.',
                ]);
            }
        }

        return $answers;
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questions
     * @param  array<int, string>  $answers
     */
    public function store(
        User $user,
        Assessment $assessment,
        Collection $questions,
        array $answers,
    ): void {
        $attemptUuid = (string) Str::uuid();

        DB::transaction(
            function () use (
                $assessment,
                $questions,
                $answers,
                $user,
                $attemptUuid,
            ) {
                /** @var array<int, list<float>> $skillScores */
                $skillScores = [];

                foreach ($questions as $question) {
                    $questionId = (int) $question->id;

                    if (! array_key_exists($questionId, $answers)) {
                        throw new \RuntimeException(
                            'Jawaban Assesment tidak lengkap saat hasil akan disimpan.',
                        );
                    }

                    $answer = $answers[$questionId];

                    $correct = (
                        $answer
                        === $question->correct_answer
                    );

                    $score = $correct
                        ? SkillPathScoringPolicy::ASSESSMENT_CORRECT_SCORE
                        : SkillPathScoringPolicy::ASSESSMENT_INCORRECT_SCORE;

                    AssessmentResult::create([
                        'user_id' => $user->id,
                        'assessment_id' => $assessment->id,
                        'assessment_question_id' => $question->id,
                        'skill_id' => $question->skill_id,
                        'attempt_uuid' => $attemptUuid,
                        'score' => $score,
                        'is_correct' => $correct,
                        'answer' => $answer,
                        'response_text' => null,
                        'evidence_url' => null,
                        'experience_notes' => null,
                        'experience_evidence_url' => null,
                    ]);

                    $skillScores[
                        (int) $question->skill_id
                    ][] = $score;
                }

                foreach ($skillScores as $skillId => $scores) {
                    $average = round(
                        array_sum($scores)
                            / count($scores),
                        2,
                    );

                    UserSkill::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'skill_id' => $skillId,
                        ],
                        [
                            'score' => $average,
                            'source' => 'assessment',
                            'last_assessed_at' => now(),
                        ],
                    );
                }
            },
        );
    }
}
