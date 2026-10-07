<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAssessmentQuestionFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_edit_academic_multiple_choice_question(): void
    {
        $question = $this->academicQuestion();

        $data = $this->questionData($question);

        $data['prompt'] = 'Apa fungsi utama relasi antartabel dalam database?';

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.questions.update', $question),
                $data,
            )
            ->assertSessionHasNoErrors();

        $question->refresh();

        $this->assertSame(
            $data['prompt'],
            $question->prompt,
        );

        $this->assertSame(
            'multiple_choice',
            $question->question_type,
        );

        $this->assertFalse(
            (bool) $question->evidence_required,
        );
    }

    public function test_admin_cannot_add_extra_question_to_academic_assessment(): void
    {
        $question = $this->academicQuestion();

        $assessment = $question->assessment;

        $countBefore = $assessment
            ->questions()
            ->count();

        $this->assertSame(50, $countBefore);

        $this
            ->actingAs($this->admin)
            ->post(
                route('admin.questions.store'),
                $this->questionData($question),
            )
            ->assertSessionHasErrors('assessment_id');

        $this->assertSame(
            $countBefore,
            $assessment->questions()->count(),
        );
    }

    public function test_admin_cannot_delete_academic_question(): void
    {
        $question = $this->academicQuestion();

        $this
            ->actingAs($this->admin)
            ->delete(
                route('admin.questions.destroy', $question),
            )
            ->assertSessionHasErrors('question');

        $this->assertDatabaseHas(
            'assessment_questions',
            [
                'id' => $question->id,
            ],
        );
    }

    public function test_admin_cannot_move_academic_question_to_another_skill(): void
    {
        $question = $this->academicQuestion();

        $otherQuestion = $question
            ->assessment
            ->questions()
            ->where(
                'skill_id',
                '!=',
                $question->skill_id,
            )
            ->firstOrFail();

        $data = $this->questionData($question);

        $data['skill_id'] = $otherQuestion->skill_id;

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.questions.update', $question),
                $data,
            )
            ->assertSessionHasErrors('assessment_id');

        $this->assertSame(
            $question->skill_id,
            $question->fresh()->skill_id,
        );
    }

    public function test_admin_can_create_question_for_additional_assessment(): void
    {
        $career = Career::create([
            'name' => 'Jurusan Pengujian',
            'slug' => 'jurusan-pengujian',
            'tagline' => 'Jurusan khusus pengujian',
            'description' => 'Data pengujian pengelolaan soal.',
            'responsibilities' => [
                'Bidang pengujian',
            ],
            'difficulty' => 'Lintas tahap',
            'accent' => '#AAC8F5',
            'is_active' => true,
        ]);

        $assessment = Assessment::create([
            'career_id' => $career->id,
            'study_program' => null,
            'title' => 'Assessment Tambahan',
            'description' => 'Assessment untuk menguji form soal.',
            'duration_minutes' => 50,
            'is_active' => true,
        ]);

        $question = $this->academicQuestion();

        $data = $this->questionData($question);

        $data['assessment_id'] = $assessment->id;
        $data['prompt'] = 'Apa tujuan pengujian perangkat lunak?';

        unset($data['evidence_required']);

        $this
            ->actingAs($this->admin)
            ->post(
                route('admin.questions.store'),
                $data,
            )
            ->assertSessionHasNoErrors();

        $created = AssessmentQuestion::query()
            ->where('assessment_id', $assessment->id)
            ->firstOrFail();

        $this->assertSame(
            $data['prompt'],
            $created->prompt,
        );

        $this->assertSame(
            'multiple_choice',
            $created->question_type,
        );

        $this->assertFalse(
            (bool) $created->evidence_required,
        );
    }

    private function academicQuestion(): AssessmentQuestion
    {
        $assessment = Assessment::query()
            ->where(
                'study_program',
                'Sistem Informasi',
            )
            ->firstOrFail();

        return $assessment
            ->questions()
            ->firstOrFail();
    }

    /**
     * @return array<string, mixed>
     */
    private function questionData(
        AssessmentQuestion $question,
    ): array {
        return [
            'assessment_id' => $question->assessment_id,
            'skill_id' => $question->skill_id,
            'question_type' => 'multiple_choice',
            'evidence_required' => false,
            'prompt' => $question->prompt,
            'options' => array_values(
                $question->options,
            ),
            'correct_answer' => $question->correct_answer,
            'explanation' => $question->explanation,
            'difficulty' => 'Menengah',
        ];
    }
}
