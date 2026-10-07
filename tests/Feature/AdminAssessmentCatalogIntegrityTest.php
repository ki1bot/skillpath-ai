<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Career;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAssessmentCatalogIntegrityTest extends TestCase
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

    public function test_academic_assessment_cannot_be_deleted(): void
    {
        $assessment = $this->academicAssessment();

        $this->assertSame(
            50,
            $assessment->questions()->count(),
        );

        $this
            ->actingAs($this->admin)
            ->delete(
                route('admin.assessments.destroy', $assessment),
            )
            ->assertSessionHasErrors('assessment');

        $this->assertDatabaseHas(
            'assessments',
            [
                'id' => $assessment->id,
                'study_program' => 'Sistem Informasi',
            ],
        );

        $this->assertSame(
            50,
            $assessment->questions()->count(),
        );
    }

    public function test_academic_assessment_cannot_change_career(): void
    {
        $assessment = $this->academicAssessment();

        $otherCareer = Career::query()
            ->where('name', 'Manajemen')
            ->firstOrFail();

        $originalCareerId = $assessment->career_id;

        $data = $this->assessmentData($assessment);

        $data['career_id'] = $otherCareer->id;

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.assessments.update', $assessment),
                $data,
            )
            ->assertSessionHasErrors('assessment');

        $this->assertSame(
            $originalCareerId,
            $assessment->fresh()->career_id,
        );
    }

    public function test_academic_assessment_cannot_be_deactivated(): void
    {
        $assessment = $this->academicAssessment();

        $data = $this->assessmentData($assessment);

        $data['is_active'] = false;

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.assessments.update', $assessment),
                $data,
            )
            ->assertSessionHasErrors('assessment');

        $this->assertTrue(
            $assessment->fresh()->is_active,
        );
    }

    public function test_admin_can_update_academic_assessment_description(): void
    {
        $assessment = $this->academicAssessment();

        $data = $this->assessmentData($assessment);

        $data['title'] = 'Assessment Awal Sistem Informasi';

        $data['description'] = 'Jawab 50 soal pilihan ganda sesuai kemampuanmu. Hasilnya digunakan untuk menyusun prioritas belajar pada roadmap.';

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.assessments.update', $assessment),
                $data,
            )
            ->assertSessionHasNoErrors();

        $assessment->refresh();

        $this->assertSame(
            $data['description'],
            $assessment->description,
        );

        $this->assertSame(
            'Sistem Informasi',
            $assessment->study_program,
        );

        $this->assertTrue(
            $assessment->is_active,
        );

        $this->assertSame(
            50,
            $assessment->questions()->count(),
        );
    }

    public function test_additional_assessment_on_academic_career_is_not_canonical(): void
    {
        $career = Career::query()
            ->where('name', 'Sistem Informasi')
            ->firstOrFail();

        $data = [
            'career_id' => $career->id,
            'title' => 'Assessment Tambahan Sistem Informasi',
            'description' => 'Latihan pilihan ganda tambahan untuk mahasiswa.',
            'duration_minutes' => 30,
            'is_active' => true,
        ];

        $this
            ->actingAs($this->admin)
            ->post(
                route('admin.assessments.store'),
                $data,
            )
            ->assertSessionHasNoErrors();

        $assessment = Assessment::query()
            ->where('title', $data['title'])
            ->firstOrFail();

        $this->assertNull(
            $assessment->study_program,
        );

        $this->assertSame(
            $career->id,
            $assessment->career_id,
        );

        $this->assertSame(
            0,
            $assessment->questions()->count(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function assessmentData(Assessment $assessment): array
    {
        return [
            'career_id' => $assessment->career_id,
            'title' => $assessment->title,
            'description' => $assessment->description,
            'duration_minutes' => $assessment->duration_minutes,
            'is_active' => $assessment->is_active,
        ];
    }

    private function academicAssessment(): Assessment
    {
        return Assessment::query()
            ->where('study_program', 'Sistem Informasi')
            ->where('is_active', true)
            ->firstOrFail();
    }
}
