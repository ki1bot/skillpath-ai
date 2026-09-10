<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\Career;
use App\Models\User;
use App\Models\UserSkill;
use App\Support\AcademicAssessmentCatalog;
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

    public function test_assessment_waits_for_explicit_start_before_creating_random_session(): void
    {
        $assessment = $this->assessment();

        $this->assertCount(
            AcademicAssessmentCatalog::QUESTION_POOL_SIZE,
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
                    ->where(
                        'assessment.started',
                        false,
                    )
                    ->where(
                        'assessment.question_limit',
                        AcademicAssessmentCatalog::QUESTION_LIMIT,
                    )
                    ->where(
                        'assessment.reserve_question_count',
                        AcademicAssessmentCatalog::RESERVE_QUESTION_LIMIT,
                    )
                    ->has(
                        'assessment.questions',
                        0,
                    ),
            );

        $this->assertNull(
            session()->get(
                $this->questionSessionKey(
                    $assessment,
                ),
            ),
        );

        $this->assertNull(
            session()->get(
                $this->reserveQuestionSessionKey(
                    $assessment,
                ),
            ),
        );
    }

    public function test_starting_assessment_creates_twenty_five_active_and_five_reserve_questions(): void
    {
        $assessment = $this->assessment();

        $this
            ->actingAs(
                $this->user,
            )
            ->post(
                route(
                    'assessment.start',
                ),
            )
            ->assertRedirect(
                route(
                    'assessment.show',
                ),
            );

        $questionIds = $this->selectedQuestionIds(
            $assessment,
        );

        $reserveQuestionIds = $this->reserveQuestionIds(
            $assessment,
        );

        $this->assertCount(
            AcademicAssessmentCatalog::QUESTION_LIMIT,
            $questionIds,
        );

        $this->assertCount(
            AcademicAssessmentCatalog::RESERVE_QUESTION_LIMIT,
            $reserveQuestionIds,
        );

        $this->assertCount(
            AcademicAssessmentCatalog::QUESTION_LIMIT,
            array_unique(
                $questionIds,
            ),
        );

        $this->assertCount(
            AcademicAssessmentCatalog::RESERVE_QUESTION_LIMIT,
            array_unique(
                $reserveQuestionIds,
            ),
        );

        $this->assertSame(
            [],
            array_values(
                array_intersect(
                    $questionIds,
                    $reserveQuestionIds,
                ),
            ),
        );

        $this->assertCount(
            AcademicAssessmentCatalog::QUESTION_POOL_SIZE,
            array_unique(
                array_merge(
                    $questionIds,
                    $reserveQuestionIds,
                ),
            ),
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
            AcademicAssessmentCatalog::QUESTION_LIMIT,
            $questions,
        );

        $this->assertSame(
            AcademicAssessmentCatalog::SKILLS_PER_PROGRAM,
            $questions
                ->pluck('skill_id')
                ->unique()
                ->count(),
        );

        $distribution = $questions
            ->groupBy('skill_id')
            ->map(
                fn (Collection $skillQuestions) => $skillQuestions->count(),
            )
            ->sort()
            ->values()
            ->all();

        $this->assertSame(
            [
                2,
                2,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
            ],
            $distribution,
        );

        $this
            ->actingAs(
                $this->user,
            )
            ->get(
                route(
                    'assessment.show',
                ),
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('assessment')
                    ->where(
                        'assessment.started',
                        true,
                    )
                    ->has(
                        'assessment.questions',
                        AcademicAssessmentCatalog::QUESTION_LIMIT,
                    ),
            );
    }

    public function test_refresh_keeps_the_same_assessment_session(): void
    {
        $assessment = $this->assessment();

        $this->startAssessment(
            $assessment,
        );

        $questionIdsBefore = $this->selectedQuestionIds(
            $assessment,
        );

        $reserveIdsBefore = $this->reserveQuestionIds(
            $assessment,
        );

        $this
            ->actingAs(
                $this->user,
            )
            ->get(
                route(
                    'assessment.show',
                ),
            )
            ->assertOk();

        $this->assertSame(
            $questionIdsBefore,
            $this->selectedQuestionIds(
                $assessment,
            ),
        );

        $this->assertSame(
            $reserveIdsBefore,
            $this->reserveQuestionIds(
                $assessment,
            ),
        );
    }

    public function test_academic_assessment_uses_objective_answers_only(): void
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
            100.0,
            (float) $result->score,
        );

        $this->assertTrue(
            $result->is_correct,
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
            AcademicAssessmentCatalog::QUESTION_LIMIT,
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
            100.0,
            (float) $userSkill->score,
        );

        $this->assertSame(
            'assessment',
            $userSkill->source,
        );

        $this->assertNotNull(
            $userSkill->last_assessed_at,
        );

        $this->assertNull(
            session()->get(
                $this->questionSessionKey(
                    $assessment,
                ),
            ),
        );

        $this->assertNull(
            session()->get(
                $this->reserveQuestionSessionKey(
                    $assessment,
                ),
            ),
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
            ->take(
                AcademicAssessmentCatalog::QUESTION_LIMIT,
            )
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
            ->with(
                'questions.skill',
            )
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
            ->post(
                route(
                    'assessment.start',
                ),
            )
            ->assertRedirect(
                route(
                    'assessment.show',
                ),
            );

        $this
            ->actingAs(
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
     * @return list<int>
     */
    private function reserveQuestionIds(
        Assessment $assessment,
    ): array {
        $questionIds = session()->get(
            $this->reserveQuestionSessionKey(
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
     *     answers: array<int, string>
     * }
     */
    private function validPayload(
        Collection $questions,
    ): array {
        $answers = [];

        foreach ($questions as $question) {
            $answers[
                $question->id
            ] = $question
                ->correct_answer;
        }

        return [
            'answers' => $answers,
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

    private function reserveQuestionSessionKey(
        Assessment $assessment,
    ): string {
        return 'assessment.reserve_question_ids.'
            .$assessment->id
            .'.'
            .$this->user->id;
    }
}
