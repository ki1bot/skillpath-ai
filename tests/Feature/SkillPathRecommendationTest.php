<?php

namespace Tests\Feature;

use App\Models\LearningMaterial;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Models\User;
use App\Models\UserProject;
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
        $user = $this->user->fresh([
            'targetCareer',
        ]);

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

        $roadmap = app(
            RoadmapService::class,
        )->regenerate(
            $user->fresh([
                'targetCareer',
            ]),
            'Pengujian gap pemrograman',
        );

        $first = $roadmap
            ->items()
            ->with(
                'material.skill',
            )
            ->orderBy(
                'position',
            )
            ->firstOrFail();

        $this->assertSame(
            'programming-fundamentals',
            $first
                ->material
                ->skill
                ->slug,
        );
    }

    public function test_database_gap_is_prioritized_before_dependent_backend_skills(): void
    {
        $user = $this->user->fresh([
            'targetCareer',
        ]);

        $database = $user
            ->targetCareer
            ->skills()
            ->where(
                'skills.slug',
                'database-fundamentals',
            )
            ->firstOrFail();

        $programming = $user
            ->targetCareer
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

        $roadmap = app(
            RoadmapService::class,
        )->regenerate(
            $user->fresh([
                'targetCareer',
            ]),
            'Pengujian gap database',
        );

        $items = $roadmap
            ->items()
            ->with(
                'material.skill',
            )
            ->get()
            ->keyBy(
                fn (RoadmapItem $item): string => $item
                    ->material
                    ->skill
                    ->slug,
            );

        $this->assertLessThan(
            $items[
                'eloquent-orm'
            ]->position,
            $items[
                'database-fundamentals'
            ]->position,
        );

        $this->assertSame(
            'available',
            $items[
                'database-fundamentals'
            ]->status,
        );

        $this->assertSame(
            'locked',
            $items[
                'rest-api'
            ]->status,
        );
    }

    public function test_active_roadmap_reorders_future_items_without_recreating_the_roadmap(): void
    {
        $roadmap = Roadmap::query()
            ->where(
                'user_id',
                $this->user->id,
            )
            ->where(
                'is_active',
                true,
            )
            ->firstOrFail();

        $items = $roadmap
            ->items()
            ->with('material.skill')
            ->get()
            ->keyBy(
                fn (RoadmapItem $item): string => $item
                    ->material
                    ->skill
                    ->slug,
            );

        $database = $items[
            'database-fundamentals'
        ];
        $restApi = $items[
            'rest-api'
        ];

        $databasePosition = $database->position;
        $restApiPosition = $restApi->position;

        $database->update([
            'position' => $restApiPosition,
        ]);

        $restApi->update([
            'position' => $databasePosition,
        ]);

        app(RoadmapService::class)
            ->adaptAfterSkillChange(
                $this->user->fresh([
                    'targetCareer',
                ]),
                'Pengujian pengurutan ulang roadmap',
            );

        $this->assertSame(
            $roadmap->id,
            Roadmap::query()
                ->where(
                    'user_id',
                    $this->user->id,
                )
                ->where(
                    'is_active',
                    true,
                )
                ->value('id'),
        );

        $database->refresh();
        $restApi->refresh();

        $this->assertLessThan(
            $restApi->position,
            $database->position,
        );

        $this->assertDatabaseHas(
            'progress_logs',
            [
                'user_id' => $this->user->id,
                'activity_type' => 'roadmap_reordered',
            ],
        );
    }

    public function test_evaluation_requires_external_evidence_and_meaningful_reflection(): void
    {
        [$material, $item] = $this->databaseMaterialAndRoadmapItem();

        $before = (float) UserSkill::query()
            ->where(
                'user_id',
                $this->user->id,
            )
            ->where(
                'skill_id',
                $material->skill_id,
            )
            ->value('score');

        $this->actingAs(
            $this->user,
        )
            ->post(
                route(
                    'roadmap.evaluate',
                    $item,
                ),
                [
                    'answer' => $material
                        ->quiz_answer,
                    'reflection' => $this
                        ->validReflection(),
                ],
            )
            ->assertSessionHasErrors([
                'practical_evidence_url',
            ]);

        $after = (float) UserSkill::query()
            ->where(
                'user_id',
                $this->user->id,
            )
            ->where(
                'skill_id',
                $material->skill_id,
            )
            ->value('score');

        $this->assertSame(
            $before,
            $after,
        );

        $item->refresh();

        $this->assertNotSame(
            'completed',
            $item->status,
        );
    }

    public function test_private_or_non_external_evidence_is_rejected(): void
    {
        [$material, $item] = $this->databaseMaterialAndRoadmapItem();

        $this->actingAs(
            $this->user,
        )
            ->post(
                route(
                    'roadmap.evaluate',
                    $item,
                ),
                [
                    'answer' => $material
                        ->quiz_answer,
                    'practical_evidence_url' => 'https://localhost/evidence',
                    'reflection' => $this
                        ->validReflection(),
                ],
            )
            ->assertSessionHasErrors([
                'practical_evidence_url',
            ]);
    }

    public function test_failed_evaluation_does_not_increase_skill_score_and_adds_reinforcement(): void
    {
        $user = $this->user->fresh([
            'targetCareer',
        ]);

        [$material, $item] = $this->databaseMaterialAndRoadmapItem();

        $roadmap = $item
            ->roadmap()
            ->firstOrFail();

        $before = (float) UserSkill::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->where(
                'skill_id',
                $material->skill_id,
            )
            ->value(
                'score',
            );

        $wrongAnswer = collect(
            array_keys(
                $material->quiz_options,
            ),
        )->first(
            fn (string $answer): bool => $answer
                !== $material->quiz_answer,
        );

        $this->assertNotNull(
            $wrongAnswer,
        );

        $this->actingAs(
            $user,
        )
            ->post(
                route(
                    'roadmap.evaluate',
                    $item,
                ),
                [
                    'answer' => $wrongAnswer,
                    'practical_evidence_url' => 'https://example.com/evidence/database-failure',
                    'reflection' => $this
                        ->validReflection(),
                ],
            )
            ->assertRedirect(
                route(
                    'roadmap.index',
                ),
            );

        $after = (float) UserSkill::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->where(
                'skill_id',
                $material->skill_id,
            )
            ->value(
                'score',
            );

        $this->assertSame(
            $before,
            $after,
        );

        $item->refresh();

        $this->assertSame(
            'reinforcement_required',
            $item->status,
        );

        $reinforcementItem = RoadmapItem::query()
            ->where(
                'roadmap_id',
                $roadmap->id,
            )
            ->where(
                'reinforcement_for_roadmap_item_id',
                $item->id,
            )
            ->with(
                'material',
            )
            ->first();

        $this->assertNotNull(
            $reinforcementItem,
        );

        $this->assertSame(
            'reinforcement',
            $reinforcementItem
                ->material
                ->material_type,
        );

        $this->assertSame(
            'available',
            $reinforcementItem
                ->status,
        );

        $this->assertSame(
            $material->id,
            $reinforcementItem
                ->material
                ->reinforcement_for_material_id,
        );

        $this->assertGreaterThanOrEqual(
            1,
            $item->reinforcement_count,
        );

        $this->assertGreaterThanOrEqual(
            1,
            $item->evaluation_attempts,
        );
    }

    public function test_passing_database_evaluation_unlocks_beginner_backend_project(): void
    {
        $user = $this->user->fresh([
            'targetCareer',
        ]);

        [$material, $item] = $this->databaseMaterialAndRoadmapItem();

        $project = $user
            ->targetCareer
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

        $this->actingAs(
            $user,
        )
            ->post(
                route(
                    'roadmap.evaluate',
                    $item,
                ),
                [
                    'answer' => $material
                        ->quiz_answer,
                    'practical_evidence_url' => 'https://github.com/ki1bot/skillpath-ai',
                    'reflection' => $this
                        ->validReflection(),
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

    public function test_projects_are_separated_into_recommended_strengthen_and_challenge_states(): void
    {
        $user = $this->user->fresh([
            'targetCareer',
        ]);

        $service = app(
            ProjectReadinessService::class,
        );

        $beginner = $user
            ->targetCareer
            ->projects()
            ->where(
                'slug',
                'task-management-api',
            )
            ->firstOrFail();

        $advanced = $user
            ->targetCareer
            ->projects()
            ->where(
                'slug',
                'sistem-reservasi-ruangan-api',
            )
            ->firstOrFail();

        $this->assertSame(
            'strengthen',
            $service->calculate(
                $user,
                $beginner,
            )['recommendation']['level'],
        );

        $this->assertSame(
            'challenge',
            $service->calculate(
                $user,
                $advanced,
            )['recommendation']['level'],
        );

        $beginner->load('skills');

        foreach ($beginner->skills as $skill) {
            UserSkill::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'skill_id' => $skill->id,
                ],
                [
                    'score' => $skill
                        ->pivot
                        ->required_level,
                    'source' => 'evaluation',
                    'last_assessed_at' => now(),
                ],
            );
        }

        $this->assertSame(
            'recommended',
            $service->calculate(
                $user->fresh(),
                $beginner->fresh(),
            )['recommendation']['level'],
        );
    }

    public function test_project_cannot_be_completed_without_external_evidence_and_completion_notes(): void
    {
        $project = $this->user
            ->targetCareer
            ->projects()
            ->where(
                'slug',
                'task-management-api',
            )
            ->firstOrFail();

        $this->actingAs(
            $this->user,
        )
            ->post(
                route(
                    'projects.start',
                    $project,
                ),
            )
            ->assertRedirect();

        $this->actingAs(
            $this->user,
        )
            ->patch(
                route(
                    'projects.update',
                    $project,
                ),
                [
                    'progress_percentage' => 100,
                    'repository_url' => '',
                    'notes' => $this
                        ->validProjectCompletionNotes(),
                ],
            )
            ->assertSessionHasErrors([
                'repository_url',
            ]);

        $this->actingAs(
            $this->user,
        )
            ->patch(
                route(
                    'projects.update',
                    $project,
                ),
                [
                    'progress_percentage' => 100,
                    'repository_url' => 'https://github.com/ki1bot/skillpath-ai',
                    'notes' => $this
                        ->validProjectCompletionNotes(),
                ],
            )
            ->assertSessionHasNoErrors();

        $userProject = UserProject::query()
            ->where(
                'user_id',
                $this->user->id,
            )
            ->where(
                'portfolio_project_id',
                $project->id,
            )
            ->firstOrFail();

        $this->assertSame(
            'completed',
            $userProject->status,
        );

        $this->assertSame(
            100,
            (int) $userProject
                ->progress_percentage,
        );

        $this->assertNotNull(
            $userProject->completed_at,
        );
    }

    private function databaseMaterialAndRoadmapItem(): array
    {
        $material = LearningMaterial::query()
            ->where(
                'material_type',
                'core',
            )
            ->where(
                'is_active',
                true,
            )
            ->whereHas(
                'skill',
                fn ($query) => $query->where(
                    'slug',
                    'database-fundamentals',
                ),
            )
            ->firstOrFail();

        $item = Roadmap::query()
            ->where(
                'user_id',
                $this->user->id,
            )
            ->where(
                'is_active',
                true,
            )
            ->firstOrFail()
            ->items()
            ->where(
                'learning_material_id',
                $material->id,
            )
            ->firstOrFail();

        return [
            $material,
            $item,
        ];
    }

    private function validReflection(): string
    {
        return 'Saya memahami konsep utama materi, mencoba latihan praktik, menemukan bagian yang sempat salah, lalu memperbaikinya dan memastikan hasil akhirnya berjalan sesuai tujuan pembelajaran.';
    }

    private function validProjectCompletionNotes(): string
    {
        return 'Seluruh fitur minimum sudah selesai, alur utama sudah diuji, repository berisi implementasi terbaru, dan bukti pada tautan yang disertakan menunjukkan hasil proyek yang dapat diperiksa kembali.';
    }
}
