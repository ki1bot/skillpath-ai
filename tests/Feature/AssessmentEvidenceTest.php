<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\Career;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AssessmentEvidenceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $career = Career::query()
            ->where(
                'slug',
                'sistem-informasi',
            )
            ->where(
                'is_active',
                true,
            )
            ->firstOrFail();

        $this->user = User::factory()
            ->create([
                'name' => 'Pengguna Pengujian Assesment',
                'email' => 'assessment-academic@example.test',
                'role' => 'student',
                'study_program' => 'Sistem Informasi',
                'semester' => 5,
                'interest_area' => 'Analisis Data dan Pengembangan Sistem',
                'experience' => 'Pengguna khusus untuk pengujian Assesment akademik.',
                'weekly_study_hours' => 8,
                'target_career_id' => $career->id,
                'onboarding_completed_at' => now(),
            ]);
    }

    public function test_academic_assessment_displays_twenty_five_random_multiple_choice_questions(): void
    {
        $assessment = $this->assessment();

        $this->assertCount(
            30,
            $assessment->questions,
        );

        $response = $this
            ->actingAs(
                $this->user,
            )
            ->get(
                route(
                    'assessment.show',
                ),
            );

        $response
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('assessment')
                    ->where(
                        'assessment.study_program',
                        'Sistem Informasi',
                    )
                    ->has(
                        'assessment.questions',
                        25,
                    ),
            );

        $questionIds = $this->selectedQuestionIds(
            $assessment,
        );

        $this->assertCount(
            25,
            $questionIds,
        );

        $this->assertCount(
            25,
            array_unique($questionIds),
        );

        $questions = AssessmentQuestion::query()
            ->where(
                'assessment_id',
                $assessment->id,
            )
            ->whereIn(
                'id',
                $questionIds,
            )
            ->get();

        $this->assertCount(
            25,
            $questions,
        );

        foreach ($questions as $question) {
            $this->assertSame(
                'multiple_choice',
                $question->question_type,
            );

            $this->assertFalse(
                $question->evidence_required,
            );

            $this->assertNull(
                $question->practical_instructions,
            );

            $this->assertCount(
                4,
                $question->options,
            );
        }
    }

    public function test_academic_assessment_uses_objective_answer_and_self_rating_components(): void
    {
        $assessment = $this->assessment();

        $questions = $this->startAssessment(
            $assessment,
        );

        $payload = $this->validPayload(
            $questions,
        );

        $question = $questions->first();

        $this->assertNotNull(
            $question,
        );

        $this->actingAs(
            $this->user,
        )
            ->post(
                route(
                    'assessment.submit',
                ),
                $payload,
            )
            ->assertRedirect(
                route(
                    'skills.index',
                ),
            );

        $result = AssessmentResult::query()
            ->where(
                'user_id',
                $this->user->id,
            )
            ->where(
                'assessment_id',
                $assessment->id,
            )
            ->where(
                'assessment_question_id',
                $question->id,
            )
            ->latest()
            ->firstOrFail();

        $this->assertSame(
            90.0,
            (float) $result->score,
        );

        $this->assertTrue(
            $result->is_correct,
        );

        $this->assertSame(
            50,
            $result->self_rating,
        );

        $this->assertNull(
            $result->response_text,
        );

        $this->assertNull(
            $result->evidence_url,
        );

        $this->assertNull(
            $result->experience_notes,
        );

        $this->assertNull(
            $result->experience_evidence_url,
        );

        $this->assertSame(
            25,
            AssessmentResult::query()
                ->where(
                    'user_id',
                    $this->user->id,
                )
                ->where(
                    'assessment_id',
                    $assessment->id,
                )
                ->where(
                    'attempt_uuid',
                    $result->attempt_uuid,
                )
                ->count(),
        );

        $userSkill = UserSkill::query()
            ->where(
                'user_id',
                $this->user->id,
            )
            ->where(
                'skill_id',
                $question->skill_id,
            )
            ->firstOrFail();

        $this->assertSame(
            90.0,
            (float) $userSkill->score,
        );

        $this->assertSame(
            'assessment',
            $userSkill->source,
        );

        $this->assertNotNull(
            $userSkill->last_assessed_at,
        );
    }

    public function test_academic_assessment_rejects_incomplete_answers(): void
    {
        $assessment = $this->assessment();

        $questions = $this->startAssessment(
            $assessment,
        );

        $payload = $this->validPayload(
            $questions,
        );

        $question = $questions->first();

        $this->assertNotNull(
            $question,
        );

        unset(
            $payload['answers'][
                $question->id
            ],
        );

        $this->actingAs(
            $this->user,
        )
            ->post(
                route(
                    'assessment.submit',
                ),
                $payload,
            )
            ->assertSessionHasErrors([
                'answers',
            ]);

        $this->assertDatabaseMissing(
            'assessment_results',
            [
                'user_id' => $this
                    ->user
                    ->id,
                'assessment_id' => $assessment
                    ->id,
                'assessment_question_id' => $question
                    ->id,
            ],
        );
    }

    public function test_assessment_submission_requires_active_random_question_session(): void
    {
        $assessment = $this->assessment();

        $questions = $assessment
            ->questions
            ->take(25)
            ->values();

        $payload = $this->validPayload(
            $questions,
        );

        $this->actingAs(
            $this->user,
        )
            ->post(
                route(
                    'assessment.submit',
                ),
                $payload,
            )
            ->assertRedirect(
                route(
                    'assessment.show',
                ),
            );

        $this->assertSame(
            0,
            AssessmentResult::query()
                ->where(
                    'user_id',
                    $this->user->id,
                )
                ->where(
                    'assessment_id',
                    $assessment->id,
                )
                ->count(),
        );
    }

    private function assessment(): Assessment
    {
        return Assessment::query()
            ->where(
                'career_id',
                $this->user
                    ->target_career_id,
            )
            ->where(
                'study_program',
                'Sistem Informasi',
            )
            ->where(
                'is_active',
                true,
            )
            ->with('questions')
            ->firstOrFail();
    }

    /**
     * @return Collection<int, AssessmentQuestion>
     */
    private function startAssessment(
        Assessment $assessment,
    ): Collection {
        $this->actingAs(
            $this->user,
        )
            ->get(
                route(
                    'assessment.show',
                ),
            )
            ->assertOk();

        $questionIds = $this->selectedQuestionIds(
            $assessment,
        );

        return AssessmentQuestion::query()
            ->where(
                'assessment_id',
                $assessment->id,
            )
            ->whereIn(
                'id',
                $questionIds,
            )
            ->get();
    }

    /**
     * @return list<int>
     */
    private function selectedQuestionIds(
        Assessment $assessment,
    ): array {
        $questionIds = session()->get(
            $this->questionSessionKey(
                $assessment,
            ),
        );

        $this->assertIsArray(
            $questionIds,
        );

        return collect(
            $questionIds,
        )
            ->map(
                fn ($id) => (int) $id,
            )
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questions
     * @return array{
     *     answers: array<int, string>,
     *     self_ratings: array<int, int>
     * }
     */
    private function validPayload(
        Collection $questions,
    ): array {
        $answers = [];
        $ratings = [];

        foreach ($questions as $question) {
            $answers[
                $question->id
            ] = $question
                ->correct_answer;

            $ratings[
                $question->id
            ] = 50;
        }

        return [
            'answers' => $answers,
            'self_ratings' => $ratings,
        ];
    }

    private function questionSessionKey(
        Assessment $assessment,
    ): string {
        return 'assessment.question_ids.'
            .$assessment->id
            .'.'
            .$this->user->id;
    }
}
