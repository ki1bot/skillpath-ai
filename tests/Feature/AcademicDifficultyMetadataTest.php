<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\Skill;
use App\Support\AcademicProgramCatalog;
use Database\Seeders\AcademicAssessmentSkillSeeder;
use Database\Seeders\CareerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicDifficultyMetadataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_academic_skills_use_cross_stage_metadata(): void
    {
        $slugs = AcademicProgramCatalog::allSkillSlugs();

        $this->assertCount(54, $slugs);

        $this->assertSame(
            54,
            Skill::query()
                ->whereIn('slug', $slugs)
                ->where('difficulty', 'Lintas tahap')
                ->count(),
        );
    }

    public function test_academic_programs_use_cross_stage_metadata(): void
    {
        $names = array_keys(
            AcademicProgramCatalog::programs(),
        );

        $this->assertCount(6, $names);

        $this->assertSame(
            6,
            Career::query()
                ->whereIn('name', $names)
                ->where('difficulty', 'Lintas tahap')
                ->count(),
        );
    }

    public function test_reseeding_skills_preserves_admin_description(): void
    {
        $slug = AcademicProgramCatalog::allSkillSlugs()[0];

        $skill = Skill::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $skill->update([
            'description' => 'Deskripsi kemampuan yang sudah diperbarui admin.',
            'difficulty' => 'Dasar',
        ]);

        $this->seed(
            AcademicAssessmentSkillSeeder::class,
        );

        $skill->refresh();

        $this->assertSame(
            'Deskripsi kemampuan yang sudah diperbarui admin.',
            $skill->description,
        );

        $this->assertSame(
            'Lintas tahap',
            $skill->difficulty,
        );
    }

    public function test_reseeding_careers_preserves_admin_description(): void
    {
        $career = Career::query()
            ->where('name', 'Sistem Informasi')
            ->firstOrFail();

        $career->update([
            'description' => 'Deskripsi jurusan yang sudah diperbarui admin.',
            'difficulty' => 'Menengah',
        ]);

        $this->seed(
            CareerSeeder::class,
        );

        $career->refresh();

        $this->assertSame(
            'Deskripsi jurusan yang sudah diperbarui admin.',
            $career->description,
        );

        $this->assertSame(
            'Lintas tahap',
            $career->difficulty,
        );
    }
}
