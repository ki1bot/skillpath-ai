<?php

namespace Tests\Feature;

use App\Models\Evaluation;
use App\Models\LearningMaterial;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Models\User;
use App\Models\UserSkill;
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

    public function test_weakest_academic_skill_is_prioritized_first(): void
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
            'si-database-management',
            $first
                ->material
                ->skill
                ->slug,
        );
    }

    public function test_generated_roadmap_contains_only_selected_program_skills(): void
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
            ->with(
                'items.material.skill',
            )
            ->firstOrFail();

        $this->assertNotEmpty(
            $roadmap->items,
        );

        foreach ($roadmap->items as $item) {
            $this->assertStringStartsWith(
                'si-',
                $item
                    ->material
                    ->skill
                    ->slug,
            );
        }
    }

    public function test_evaluation_requires_google_drive_evidence(): void
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
                ],
            )
            ->assertSessionHasErrors([
                'practical_evidence_url',
            ]);

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
                    'practical_evidence_url' => 'https://github.com/example/project',
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

    public function test_evaluation_accepts_google_drive_evidence_without_reflection(): void
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
                    'practical_evidence_url' => 'https://drive.google.com/file/d/skillpath-evidence/view',
                ],
            )
            ->assertSessionHasNoErrors();

        $item->refresh();

        $this->assertSame(
            'completed',
            $item->status,
        );

        $this->assertSame(
            100.0,
            (float) $item->evaluation_score,
        );

        $evaluation = Evaluation::query()
            ->where(
                'user_id',
                $this->user->id,
            )
            ->where(
                'roadmap_item_id',
                $item->id,
            )
            ->latest('id')
            ->firstOrFail();

        $this->assertTrue(
            $evaluation->passed,
        );

        $this->assertSame(
            100.0,
            $evaluation->score,
        );

        $this->assertSame(
            80.0,
            $evaluation->knowledge_score,
        );

        $this->assertSame(
            20.0,
            $evaluation->evidence_score,
        );

        $this->assertSame(
            0.0,
            $evaluation->reflection_score,
        );

        $this->assertNull(
            $evaluation->reflection,
        );
    }

    public function test_failed_evaluation_does_not_increase_skill_score_and_adds_reinforcement(): void
    {
        [$material, $item] = $this->databaseMaterialAndRoadmapItem();

        $roadmap = $item
            ->roadmap()
            ->firstOrFail();

        $before = (float) UserSkill::query()
            ->where(
                'user_id',
                $this->user->id,
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
            $this->user,
        )
            ->post(
                route(
                    'roadmap.evaluate',
                    $item,
                ),
                [
                    'answer' => $wrongAnswer,
                    'practical_evidence_url' => 'https://drive.google.com/file/d/database-failure/view',
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
                $this->user->id,
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
                    'si-database-management',
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
}
