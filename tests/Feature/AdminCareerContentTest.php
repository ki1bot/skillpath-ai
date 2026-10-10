<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCareerContentTest extends TestCase
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

    public function test_admin_can_update_academic_display_name_and_area_labels(): void
    {
        $career = Career::query()
            ->where('slug', 'sistem-informasi')
            ->firstOrFail();

        $originalId = $career->id;
        $originalSlug = $career->slug;

        $originalSkills = $career->skills()
            ->pluck('skills.id')
            ->sort()
            ->values()
            ->all();

        $data = $this->careerData($career);

        $data['display_name'] = 'Sistem Informasi dan Bisnis Digital';

        $data['responsibilities'] = [
            'Analisis Data Bisnis',
            'Pengembangan Sistem Informasi',
            'Desain Pengalaman Pengguna',
        ];

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.careers.update', $career),
                $data,
            )
            ->assertSessionHasNoErrors();

        $career->refresh();

        $this->assertSame($originalId, $career->id);
        $this->assertSame($originalSlug, $career->slug);

        $this->assertSame(
            'Sistem Informasi',
            $career->name,
        );

        $this->assertSame(
            'Sistem Informasi dan Bisnis Digital',
            $career->display_name,
        );

        $this->assertSame(
            $data['responsibilities'],
            $career->responsibilities,
        );

        $this->assertSame(
            $originalSkills,
            $career->skills()
                ->pluck('skills.id')
                ->sort()
                ->values()
                ->all(),
        );
    }

    public function test_admin_cannot_change_academic_identity(): void
    {
        $career = Career::query()
            ->where('slug', 'sistem-informasi')
            ->firstOrFail();

        $data = $this->careerData($career);

        $data['name'] = 'Nama Jurusan Pengganti';

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.careers.update', $career),
                $data,
            )
            ->assertSessionHasErrors('name');

        $this->assertSame(
            'Sistem Informasi',
            $career->fresh()->name,
        );
    }

    public function test_admin_cannot_remove_required_academic_area(): void
    {
        $career = Career::query()
            ->where('slug', 'psikologi')
            ->firstOrFail();

        $data = $this->careerData($career);

        $data['responsibilities'] = [
            'Psikologi Industri dan Organisasi',
            'Konseling',
        ];

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.careers.update', $career),
                $data,
            )
            ->assertSessionHasErrors('responsibilities');

        $this->assertCount(
            3,
            $career->fresh()->responsibilities,
        );
    }

    public function test_admin_cannot_delete_academic_career(): void
    {
        $career = Career::query()
            ->where('slug', 'sistem-informasi')
            ->firstOrFail();

        $this
            ->actingAs($this->admin)
            ->delete(
                route('admin.careers.destroy', $career),
            )
            ->assertSessionHasErrors('career');

        $this->assertDatabaseHas('careers', [
            'id' => $career->id,
            'slug' => 'sistem-informasi',
        ]);
    }

    public function test_admin_can_edit_and_remove_additional_areas(): void
    {
        $career = $this->additionalCareer();

        $data = $this->careerData($career);

        $data['name'] = 'Jurusan Pengembangan Digital';

        $data['responsibilities'] = [
            'Pengembangan Aplikasi',
            'Desain Digital',
        ];

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.careers.update', $career),
                $data,
            )
            ->assertSessionHasNoErrors();

        $career->refresh();

        $this->assertSame(
            'Jurusan Pengembangan Digital',
            $career->name,
        );

        $this->assertSame(
            'jurusan-teknologi-digital',
            $career->slug,
        );

        $this->assertCount(
            2,
            $career->responsibilities,
        );
    }

    public function test_admin_can_delete_unused_additional_career(): void
    {
        $career = $this->additionalCareer();

        $this
            ->actingAs($this->admin)
            ->delete(
                route('admin.careers.destroy', $career),
            )
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('careers', [
            'id' => $career->id,
        ]);
    }

    public function test_admin_cannot_delete_career_used_by_student(): void
    {
        $career = $this->additionalCareer();

        User::factory()->create([
            'role' => 'student',
            'target_career_id' => $career->id,
        ]);

        $this
            ->actingAs($this->admin)
            ->delete(
                route('admin.careers.destroy', $career),
            )
            ->assertSessionHasErrors('career');

        $this->assertDatabaseHas('careers', [
            'id' => $career->id,
        ]);
    }

    public function test_student_cannot_update_career(): void
    {
        $career = Career::query()
            ->where('slug', 'sistem-informasi')
            ->firstOrFail();

        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this
            ->actingAs($student)
            ->put(
                route('admin.careers.update', $career),
                $this->careerData($career),
            )
            ->assertForbidden();
    }

    private function additionalCareer(): Career
    {
        return Career::create([
            'name' => 'Jurusan Teknologi Digital',
            'slug' => 'jurusan-teknologi-digital',
            'tagline' => 'Mempelajari teknologi digital.',
            'description' => 'Jurusan tambahan untuk pengembangan teknologi.',
            'responsibilities' => [
                'Pengembangan Aplikasi',
                'Desain Digital',
                'Analisis Teknologi',
            ],
            'difficulty' => 'Lintas tahap',
            'accent' => '#C7FF5E',
            'is_active' => true,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function careerData(Career $career): array
    {
        return [
            'name' => $career->name,
            'display_name' => $career->display_name,
            'tagline' => $career->tagline,
            'description' => $career->description,
            'responsibilities' => $career->responsibilities,
            'difficulty' => $career->difficulty,
            'accent' => $career->accent,
            'is_active' => $career->is_active,
        ];
    }
}
