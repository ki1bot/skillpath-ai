<?php

namespace Tests\Feature;

use App\Models\LearningMaterial;
use App\Models\Roadmap;
use App\Models\User;
use App\Models\UserSkill;
use App\Services\ProjectReadinessService;
use App\Services\RoadmapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesSkillPathRecommendationUser;
use Tests\TestCase;

class SkillPathRecommendationTest extends TestCase
{
    use CreatesSkillPathRecommendationUser;
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->user = $this->createSkillPathRecommendationUser();
    }

    public function test_weak_programming_is_placed_before_advanced_backend_skills(): void
    {
        $user = $this->user->fresh(['targetCareer']);

        $programming = $user->targetCareer
            ->skills()
            ->where(
                'skills.slug',
                'programming-fundamentals',
            )
            ->firstOrFail();

        UserSkill::updateOrCreate(
            [
                'user_id' => $user->id,
                'skill_id' => $programming->id,
            ],
            [
                'score' => 20,
                'source' => 'assessment',
                'last_assessed_at' => now(),
            ],
        );

        $roadmap = app(RoadmapService::class)
            ->regenerate(
                $user->fresh(['targetCareer']),
                'Pengujian gap pemrograman',
            );

        $first = $roadmap->items()
            ->with('material.skill')
            ->orderBy('position')
            ->firstOrFail();

        $this->assertSame(
            'programming-fundamentals',
            $first->material->skill->slug,
        );
    }

    public function test_database_gap_is_prioritized_before_dependent_backend_skills(): void
    {
        $user = $this->user->fresh(['targetCareer']);

        $database = $user->targetCareer
            ->skills()
            ->where(
                'skills.slug',
                'database-fundamentals',
            )
            ->firstOrFail();

        $programming = $user->targetCareer
            ->skills()
            ->where(
                'skills.slug',
                'programming-fundamentals',
            )
            ->firstOrFail();

        UserSkill::updateOrCreate(
            [
                'user_id' => $user->id,
                'skill_id' => $programming->id,
            ],
            [
                'score' => 90,
                'source' => 'assessment',
                'last_assessed_at' => now(),
            ],
        );

        UserSkill::updateOrCreate(
            [
                'user_id' => $user->id,
                'skill_id' => $database->id,
            ],
            [
                'score' => 20,
                'source' => 'assessment',
                'last_assessed_at' => now(),
            ],
        );

        $roadmap = app(RoadmapService::class)
            ->regenerate(
                $user->fresh(['targetCareer']),
                'Pengujian gap database',
            );

        $items = $roadmap->items()
            ->with('material.skill')
            ->get()
            ->keyBy(
                fn ($item) => $item->material->skill->slug,
            );

        $this->assertLessThan(
            $items['eloquent-orm']->position,
            $items['database-fundamentals']->position,
        );

        $this->assertSame(
            'available',
            $items['database-fundamentals']->status,
        );

        $this->assertSame(
            'locked',
            $items['rest-api']->status,
        );
    }

    public function test_failed_evaluation_does_not_increase_skill_score(): void
    {
        $user = $this->user->fresh(['targetCareer']);

        $material = LearningMaterial::query()
            ->whereHas(
                'skill',
                fn ($query) => $query->where(
                    'slug',
                    'database-fundamentals',
                ),
            )
            ->firstOrFail();

        $item = Roadmap::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->firstOrFail()
            ->items()
            ->where(
                'learning_material_id',
                $material->id,
            )
            ->firstOrFail();

        $before = (float) UserSkill::query()
            ->where('user_id', $user->id)
            ->where(
                'skill_id',
                $material->skill_id,
            )
            ->value('score');

        $wrongAnswer = collect(
            array_keys($material->quiz_options),
        )->first(
            fn ($answer) => $answer !== $material->quiz_answer,
        );

        $this->actingAs($user)
            ->post(
                route(
                    'roadmap.evaluate',
                    $item,
                ),
                [
                    'answer' => $wrongAnswer,
                ],
            )
            ->assertRedirect();

        $after = (float) UserSkill::query()
            ->where('user_id', $user->id)
            ->where(
                'skill_id',
                $material->skill_id,
            )
            ->value('score');

        $this->assertSame(
            $before,
            $after,
        );

        $this->assertSame(
            'needs_reinforcement',
            $item->fresh()->status,
        );
    }

    public function test_passing_database_evaluation_unlocks_beginner_backend_project(): void
    {
        $user = $this->user->fresh(['targetCareer']);

        $material = LearningMaterial::query()
            ->whereHas(
                'skill',
                fn ($query) => $query->where(
                    'slug',
                    'database-fundamentals',
                ),
            )
            ->firstOrFail();

        $item = Roadmap::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->firstOrFail()
            ->items()
            ->where(
                'learning_material_id',
                $material->id,
            )
            ->firstOrFail();

        $project = $user->targetCareer
            ->projects()
            ->where(
                'slug',
                'task-management-api',
            )
            ->firstOrFail();

        $service = app(
            ProjectReadinessService::class,
        );

        $this->assertFalse(
            $service->calculate(
                $user,
                $project,
            )['ready'],
        );

        $this->actingAs($user)
            ->post(
                route(
                    'roadmap.evaluate',
                    $item,
                ),
                [
                    'answer' => $material->quiz_answer,
                ],
            )
            ->assertRedirect();

        $this->assertTrue(
            $service->calculate(
                $user->fresh(),
                $project->fresh(),
            )['ready'],
        );
    }
}
