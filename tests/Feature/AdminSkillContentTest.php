<?php

namespace Tests\Feature;

use App\Models\Skill;
use App\Models\User;
use App\Support\AcademicProgramCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSkillContentTest extends TestCase
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

    public function test_admin_can_edit_academic_skill_display_name(): void
    {
        $skill = Skill::query()
            ->where('slug', 'si-sql-data-processing')
            ->firstOrFail();

        $originalId = $skill->id;
        $originalName = $skill->name;
        $originalSlug = $skill->slug;
        $originalCategory = $skill->category;

        $careerIds = $skill->careers()
            ->pluck('careers.id')
            ->sort()
            ->values()
            ->all();

        $materialIds = $skill->materials()
            ->pluck('id')
            ->sort()
            ->values()
            ->all();

        $data = $this->skillData($skill);

        $data['name'] = 'Pengolahan Data Menggunakan SQL';

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.skills.update', $skill),
                $data,
            )
            ->assertSessionHasNoErrors();

        $skill->refresh();

        $this->assertSame(
            $originalId,
            $skill->id,
        );

        $this->assertSame(
            $originalName,
            $skill->name,
        );

        $this->assertSame(
            $originalSlug,
            $skill->slug,
        );

        $this->assertSame(
            $originalCategory,
            $skill->category,
        );

        $this->assertSame(
            'Pengolahan Data Menggunakan SQL',
            $skill->display_name,
        );

        $this->assertSame(
            $careerIds,
            $skill->careers()
                ->pluck('careers.id')
                ->sort()
                ->values()
                ->all(),
        );

        $this->assertSame(
            $materialIds,
            $skill->materials()
                ->pluck('id')
                ->sort()
                ->values()
                ->all(),
        );

        $this->assertContains(
            $skill->slug,
            AcademicProgramCatalog::skillSlugs('Sistem Informasi'),
        );
    }

    public function test_academic_skill_can_restore_original_display_name(): void
    {
        $skill = Skill::query()
            ->where('slug', 'si-sql-data-processing')
            ->firstOrFail();

        $skill->update([
            'display_name' => 'Nama kemampuan sementara',
        ]);

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.skills.update', $skill),
                $this->skillData($skill),
            )
            ->assertSessionHasNoErrors();

        $this->assertNull(
            $skill->fresh()->display_name,
        );
    }

    public function test_non_academic_skill_can_still_change_its_name(): void
    {
        $skill = Skill::query()->create([
            'name' => 'Kemampuan Tambahan Awal',
            'slug' => 'kemampuan-tambahan-awal',
            'category' => 'Pengembangan Tambahan',
            'description' => 'Kemampuan tambahan untuk pengujian.',
            'difficulty' => 'Lintas tahap',
        ]);

        $data = $this->skillData($skill);

        $data['name'] = 'Kemampuan Tambahan Baru';

        $this
            ->actingAs($this->admin)
            ->put(
                route('admin.skills.update', $skill),
                $data,
            )
            ->assertSessionHasNoErrors();

        $skill->refresh();

        $this->assertSame(
            'Kemampuan Tambahan Baru',
            $skill->name,
        );

        $this->assertSame(
            'kemampuan-tambahan-baru',
            $skill->slug,
        );
    }

    public function test_student_cannot_edit_academic_skill(): void
    {
        $skill = Skill::query()
            ->where('slug', 'si-sql-data-processing')
            ->firstOrFail();

        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $data = $this->skillData($skill);

        $data['name'] = 'Nama yang tidak diizinkan';

        $this
            ->actingAs($student)
            ->put(
                route('admin.skills.update', $skill),
                $data,
            )
            ->assertForbidden();

        $skill->refresh();

        $this->assertNull(
            $skill->display_name,
        );
    }

    private function skillData(Skill $skill): array
    {
        return [
            'name' => $skill->name,
            'category' => $skill->category,
            'description' => $skill->description,
            'difficulty' => $skill->difficulty,
        ];
    }
}
