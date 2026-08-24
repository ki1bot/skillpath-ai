<?php

namespace Tests\Feature;

use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillCatalogExpansionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_seed_data_contains_fifty_four_skills(): void
    {
        $this->assertSame(
            54,
            Skill::query()->count(),
        );
    }

    public function test_academic_catalog_contains_fifty_four_skills(): void
    {
        $academicSkills = $this
            ->academicSkills();

        $this->assertCount(
            54,
            $academicSkills,
        );
    }

    public function test_six_active_programs_each_have_nine_academic_skills(): void
    {
        $programSlugs = [
            'sistem-informasi',
            'manajemen',
            'teknik-informatika',
            'sistem-komputer',
            'psikologi',
            'ilmu-komunikasi',
        ];

        $careers = Career::query()
            ->whereIn(
                'slug',
                $programSlugs,
            )
            ->where(
                'is_active',
                true,
            )
            ->with('skills')
            ->get();

        $this->assertCount(
            6,
            $careers,
        );

        $this->assertSame(
            6,
            Career::query()
                ->where(
                    'is_active',
                    true,
                )
                ->count(),
        );

        $this->assertSame(
            0,
            Career::query()
                ->where(
                    'difficulty',
                    'Legacy',
                )
                ->count(),
        );

        foreach ($careers as $career) {
            $this->assertCount(
                9,
                $career->skills,
                "Jurusan {$career->name} harus memiliki tepat 9 skill.",
            );
        }
    }

    public function test_learning_catalog_contains_fifty_four_core_and_reinforcement_materials(): void
    {
        $this->assertSame(
            54,
            LearningMaterial::query()
                ->where(
                    'material_type',
                    'core',
                )
                ->where(
                    'is_active',
                    true,
                )
                ->count(),
        );

        $this->assertSame(
            54,
            LearningMaterial::query()
                ->where(
                    'material_type',
                    'reinforcement',
                )
                ->where(
                    'is_active',
                    true,
                )
                ->count(),
        );
    }

    public function test_academic_skills_have_assessment_and_learning_materials(): void
    {
        $academicSkills = $this
            ->academicSkills();

        $this->assertCount(
            54,
            $academicSkills,
        );

        foreach (
            $academicSkills as $skill
        ) {
            $this->assertTrue(
                AssessmentQuestion::query()
                    ->where(
                        'skill_id',
                        $skill->id,
                    )
                    ->exists(),
                "Skill akademik {$skill->slug} belum memiliki soal Assesment.",
            );

            $this->assertTrue(
                $skill
                    ->materials()
                    ->where(
                        'material_type',
                        'core',
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->exists(),
                "Skill akademik {$skill->slug} belum memiliki materi utama.",
            );

            $this->assertTrue(
                $skill
                    ->materials()
                    ->where(
                        'material_type',
                        'reinforcement',
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->exists(),
                "Skill akademik {$skill->slug} belum memiliki materi penguatan.",
            );
        }
    }

    private function academicSkills()
    {
        return Skill::query()
            ->where(
                'slug',
                'like',
                'si-%',
            )
            ->orWhere(
                'slug',
                'like',
                'man-%',
            )
            ->orWhere(
                'slug',
                'like',
                'ti-%',
            )
            ->orWhere(
                'slug',
                'like',
                'sk-%',
            )
            ->orWhere(
                'slug',
                'like',
                'psi-%',
            )
            ->orWhere(
                'slug',
                'like',
                'ikom-%',
            )
            ->get();
    }
}
