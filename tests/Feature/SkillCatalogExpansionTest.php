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

    /**
     * @var array<int, string>
     */
    private array $expandedSkillSlugs = [
        'data-structures-algorithms',
        'database-performance',
        'api-documentation',
        'caching-strategies',
        'browser-dom-events',
        'component-architecture',
        'frontend-testing',
        'business-metrics-kpi',
        'exploratory-data-analysis',
        'data-storytelling',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_seed_data_contains_eighty_five_skills(): void
    {
        $this->assertSame(
            85,
            Skill::query()->count(),
        );
    }

    public function test_academic_catalog_contains_forty_five_skills(): void
    {
        $academicSkills = $this
            ->academicSkills();

        $this->assertCount(
            45,
            $academicSkills,
        );
    }

    public function test_five_active_programs_each_have_nine_academic_skills(): void
    {
        $programSlugs = [
            'sistem-informasi',
            'manajemen',
            'teknik-informatika',
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
            5,
            $careers,
        );

        foreach ($careers as $career) {
            $this->assertCount(
                9,
                $career->skills,
                "Jurusan {$career->name} harus memiliki tepat 9 skill.",
            );
        }
    }

    public function test_learning_catalog_contains_eighty_five_core_and_reinforcement_materials(): void
    {
        $this->assertSame(
            85,
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
            85,
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

    public function test_expanded_skills_are_connected_to_career_material_and_assessment_data(): void
    {
        foreach (
            $this->expandedSkillSlugs as $slug
        ) {
            $skill = Skill::query()
                ->where(
                    'slug',
                    $slug,
                )
                ->firstOrFail();

            $this->assertTrue(
                $skill
                    ->careers()
                    ->exists(),
                "Skill {$slug} belum terhubung.",
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
                "Skill {$slug} belum memiliki materi utama.",
            );

            $this->assertTrue(
                AssessmentQuestion::query()
                    ->where(
                        'skill_id',
                        $skill->id,
                    )
                    ->exists(),
                "Skill {$slug} belum memiliki soal asesmen.",
            );
        }
    }

    public function test_academic_skills_have_assessment_and_learning_materials(): void
    {
        $academicSkills = $this
            ->academicSkills();

        $this->assertCount(
            45,
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
                "Skill akademik {$skill->slug} belum memiliki soal asesmen.",
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
