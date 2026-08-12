<?php

namespace Tests\Feature;

use App\Models\AssessmentQuestion;
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

    public function test_seed_data_contains_forty_skills(): void
    {
        $this->assertSame(
            40,
            Skill::query()->count(),
        );
    }

    public function test_every_seeded_skill_has_core_and_reinforcement_material(): void
    {
        $this->assertSame(
            40,
            LearningMaterial::query()
                ->where('material_type', 'core')
                ->where('is_active', true)
                ->count(),
        );

        $this->assertSame(
            40,
            LearningMaterial::query()
                ->where('material_type', 'reinforcement')
                ->where('is_active', true)
                ->count(),
        );
    }

    public function test_expanded_skills_are_connected_to_career_material_and_assessment_data(): void
    {
        foreach ($this->expandedSkillSlugs as $slug) {
            $skill = Skill::query()
                ->where('slug', $slug)
                ->firstOrFail();

            $this->assertTrue(
                $skill->careers()->exists(),
                "Skill {$slug} belum terhubung ke karier.",
            );

            $this->assertTrue(
                $skill->materials()
                    ->where('material_type', 'core')
                    ->where('is_active', true)
                    ->exists(),
                "Skill {$slug} belum memiliki materi utama.",
            );

            $this->assertTrue(
                AssessmentQuestion::query()
                    ->where('skill_id', $skill->id)
                    ->exists(),
                "Skill {$slug} belum memiliki soal assesment.",
            );
        }
    }
}
