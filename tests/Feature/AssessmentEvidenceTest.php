<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\Career;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_academic_assessment_contains_fifteen_multiple_choice_questions(): void
    {
        $assessment = $this->assessment();

        $this->assertCount(
            15,
            $assessment->questions,
        );

        foreach ($assessment->questions as $question) {
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

        $payload = $this->validPayload(
            $assessment,
        );

        $question = $assessment
            ->questions
            ->first();

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

        $payload = $this->validPayload(
            $assessment,
        );

        $question = $assessment
            ->questions
            ->first();

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

    private function validPayload(
        Assessment $assessment,
    ): array {
        $answers = [];
        $ratings = [];

        foreach (
            $assessment->questions as $question
        ) {
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
}
