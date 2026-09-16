<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Career;
use App\Models\User;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentSessionPersistenceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Assessment $assessment;

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

        $this->assessment = Assessment::query()
            ->where(
                'career_id',
                $career->id,
            )
            ->where(
                'study_program',
                'Sistem Informasi',
            )
            ->where(
                'is_active',
                true,
            )
            ->firstOrFail();

        $this->user = User::factory()->create([
            'study_program' => 'Sistem Informasi',
            'target_career_id' => $career->id,
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);
    }

    public function test_navigating_away_clears_active_assessment_session(): void
    {
        $this->startAssessment();

        $this->assertNotNull(
            session()->get(
                $this->questionSessionKey(),
            ),
        );

        $this
            ->actingAs(
                $this->user,
            )
            ->get(
                route(
                    'careers.public',
                ),
            )
            ->assertOk();

        $this->assertNull(
            session()->get(
                $this->questionSessionKey(),
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

        $this->assertNull(
            session()->get(
                $this->questionSessionKey(),
            ),
        );
    }

    public function test_active_assessment_request_survives_server_idle_window(): void
    {
        config([
            'security.idle_timeout_minutes' => 10,
        ]);

        $questionIds = $this->assessment
            ->questions()
            ->orderBy('id')
            ->pluck('id')
            ->map(
                fn ($id): int => (int) $id,
            )
            ->all();

        $this->assertCount(
            AcademicAssessmentCatalog::QUESTION_LIMIT,
            $questionIds,
        );

        $response = $this
            ->actingAs(
                $this->user,
            )
            ->withSession([
                'auth.last_activity' => now()
                    ->subMinutes(11)
                    ->timestamp,
                $this->questionSessionKey() => $questionIds,
            ])
            ->get(
                route(
                    'assessment.show',
                ),
            );

        $response->assertOk();

        $this->assertAuthenticatedAs(
            $this->user,
        );

        $this->assertSame(
            $questionIds,
            session()->get(
                $this->questionSessionKey(),
            ),
        );
    }

    private function startAssessment(): void
    {
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
    }

    private function questionSessionKey(): string
    {
        return 'assessment.question_ids.'
            .$this->assessment->id
            .'.'
            .$this->user->id;
    }
}
