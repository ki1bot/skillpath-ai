<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\ReadinessSnapshot;
use App\Models\Roadmap;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AssessmentSubmissionAtomicityTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $career = Career::query()
            ->where('name', 'Sistem Informasi')
            ->where('is_active', true)
            ->firstOrFail();

        $this->student = User::factory()->create([
            'role' => 'student',
            'study_program' => 'Sistem Informasi',
            'semester' => 5,
            'interest_area' => 'Analisis Data',
            'experience' => 'Pengguna pengujian Assessment.',
            'weekly_study_hours' => 8,
            'target_career_id' => $career->id,
            'onboarding_completed_at' => now(),
            'email_verified_at' => now(),
        ]);

        $this->assessment = Assessment::query()
            ->where('career_id', $career->id)
            ->where('study_program', 'Sistem Informasi')
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function test_assessment_submission_is_atomic_and_can_be_retried(): void
    {
        $questions = $this->assessment
            ->questions()
            ->get();

        $this->assertCount(50, $questions);

        $answers = [];

        foreach ($questions as $question) {
            /** @var AssessmentQuestion $question */
            $answers[$question->id] = $question->correct_answer;
        }

        $this
            ->actingAs($this->student)
            ->post(route('assessment.start'))
            ->assertRedirect(route('assessment.show'));

        $material = LearningMaterial::query()
            ->where(
                'slug',
                'belajar-ahli-si-sql-data-processing',
            )
            ->where('material_type', 'core')
            ->where('is_active', true)
            ->firstOrFail();

        $material->update([
            'is_active' => false,
        ]);

        $this
            ->actingAs($this->student)
            ->post(
                route('assessment.submit'),
                [
                    'answers' => $answers,
                ],
            )
            ->assertRedirect(route('assessment.show'))
            ->assertSessionHas('error');

        $this->assertSame(
            0,
            AssessmentResult::query()
                ->where('user_id', $this->student->id)
                ->count(),
        );

        $this->assertSame(
            0,
            UserSkill::query()
                ->where('user_id', $this->student->id)
                ->count(),
        );

        $this->assertSame(
            0,
            Roadmap::query()
                ->where('user_id', $this->student->id)
                ->count(),
        );

        $this->assertSame(
            0,
            ReadinessSnapshot::query()
                ->where('user_id', $this->student->id)
                ->where('trigger', 'assessment_completed')
                ->count(),
        );

        $this
            ->actingAs($this->student)
            ->get(route('assessment.show'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('assessment')
                    ->where('assessment.started', true)
                    ->has('assessment.questions', 50),
            );

        $material->update([
            'is_active' => true,
        ]);

        $this
            ->actingAs($this->student)
            ->post(
                route('assessment.submit'),
                [
                    'answers' => $answers,
                ],
            )
            ->assertRedirect(route('skills.index'));

        $this->assertSame(
            50,
            AssessmentResult::query()
                ->where('user_id', $this->student->id)
                ->where('assessment_id', $this->assessment->id)
                ->count(),
        );

        $this->assertSame(
            9,
            UserSkill::query()
                ->where('user_id', $this->student->id)
                ->count(),
        );

        $roadmap = Roadmap::query()
            ->where('user_id', $this->student->id)
            ->where('is_active', true)
            ->with('items')
            ->firstOrFail();

        $this->assertSame(
            27,
            $roadmap->items->count(),
        );

        foreach ([1, 2, 3] as $stage) {
            $this->assertSame(
                9,
                $roadmap->items
                    ->where('stage', $stage)
                    ->count(),
            );
        }

        $this->assertSame(
            1,
            $roadmap->items
                ->where('status', 'available')
                ->count(),
        );

        $this->assertSame(
            26,
            $roadmap->items
                ->where('status', 'locked')
                ->count(),
        );

        $this->assertSame(
            1,
            ReadinessSnapshot::query()
                ->where('user_id', $this->student->id)
                ->where('trigger', 'assessment_completed')
                ->count(),
        );
    }
}
