<?php

namespace Tests\Feature;

use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AcademicStatisticsDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_homepage_displays_correct_academic_statistics(): void
    {
        $this
            ->get(route('home'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('welcome')
                    ->where('stats.careers', 6)
                    ->where('stats.skills', 18)
                    ->where('stats.materials', 54),
            );
    }

    public function test_admin_dashboard_displays_correct_academic_statistics(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('admin/index')
                    ->where('stats.careers', 6)
                    ->where('stats.learningTopics', 54)
                    ->where('stats.assessmentQuestions', 300)
                    ->where('stats.projects', 18)
                    ->where('stats.materials', 324),
            );
    }

    public function test_inactive_core_materials_reduce_unique_learning_topics(): void
    {
        $skill = Skill::query()
            ->where('slug', 'si-sql-data-processing')
            ->firstOrFail();

        $updated = LearningMaterial::query()
            ->where('skill_id', $skill->id)
            ->where('material_type', 'core')
            ->where('is_active', true)
            ->whereIn('difficulty', [
                'Amatir',
                'Menengah',
                'Ahli',
            ])
            ->update([
                'is_active' => false,
            ]);

        $this->assertSame(3, $updated);

        $this
            ->get(route('home'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('welcome')
                    ->where('stats.careers', 6)
                    ->where('stats.skills', 18)
                    ->where('stats.materials', 53),
            );

        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('admin/index')
                    ->where('stats.learningTopics', 53)
                    ->where('stats.assessmentQuestions', 300)
                    ->where('stats.materials', 321),
            );
    }

    public function test_project_with_incorrect_academic_mapping_is_excluded(): void
    {
        $project = PortfolioProject::query()
            ->where(
                'slug',
                'build-mini-information-system',
            )
            ->firstOrFail();

        $project->skills()->detach();

        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('admin/index')
                    ->where('stats.careers', 6)
                    ->where('stats.learningTopics', 54)
                    ->where('stats.assessmentQuestions', 300)
                    ->where('stats.projects', 17),
            );
    }
}
