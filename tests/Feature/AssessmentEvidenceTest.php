<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesSkillPathRecommendationUser;
use Tests\TestCase;

class AssessmentEvidenceTest extends TestCase
{
    use CreatesSkillPathRecommendationUser;
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = $this->createSkillPathRecommendationUser();
    }

    public function test_practical_assessment_requires_external_evidence(): void
    {
        $assessment = $this->assessment();
        $payload = $this->validPayload($assessment);

        $practical = $assessment
            ->questions
            ->firstWhere(
                'question_type',
                'practical',
            );

        $this->assertNotNull(
            $practical,
        );

        unset(
            $payload['evidence_urls'][
                $practical->id
            ],
        );

        $this->actingAs(
            $this->user,
        )
            ->post(
                route('assessment.submit'),
                $payload,
            )
            ->assertSessionHasErrors([
                "evidence_urls.{$practical->id}",
            ]);
    }

    public function test_practical_assessment_uses_objective_response_evidence_and_self_rating_components(): void
    {
        $assessment = $this->assessment();
        $payload = $this->validPayload($assessment);

        $practical = $assessment
            ->questions
            ->firstWhere(
                'question_type',
                'practical',
            );

        $this->assertNotNull(
            $practical,
        );

        $this->actingAs(
            $this->user,
        )
            ->post(
                route('assessment.submit'),
                $payload,
            )
            ->assertRedirect(
                route('skills.index'),
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
                $practical->id,
            )
            ->latest()
            ->firstOrFail();

        $this->assertSame(
            90.0,
            (float) $result->score,
        );

        $this->assertNotNull(
            $result->evidence_url,
        );

        $this->assertGreaterThanOrEqual(
            80,
            mb_strlen(
                (string) $result->response_text,
            ),
        );
    }

    private function assessment(): Assessment
    {
        return Assessment::query()
            ->where(
                'career_id',
                $this->user->target_career_id,
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
        $responses = [];
        $evidenceUrls = [];

        foreach ($assessment->questions as $question) {
            $answers[$question->id] = $question
                ->correct_answer;
            $ratings[$question->id] = 50;

            if (
                $question->question_type
                === 'practical'
            ) {
                $responses[$question->id] = 'Saya menyelesaikan tugas praktik sesuai instruksi, memeriksa hasil yang dibuat, mencatat kendala yang muncul, lalu memperbaiki bagian yang belum sesuai sampai hasil akhirnya dapat dijalankan dan diperiksa kembali.';
                $evidenceUrls[$question->id] = "https://example.com/evidence/{$question->id}";
            }
        }

        return [
            'answers' => $answers,
            'self_ratings' => $ratings,
            'responses' => $responses,
            'evidence_urls' => $evidenceUrls,
            'experience_notes' => [],
            'experience_evidence_urls' => [],
        ];
    }
}
