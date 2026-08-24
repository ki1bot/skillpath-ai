<?php

namespace Tests\Feature;

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

    private function validReflection(): string
    {
        return 'Saya memahami konsep utama materi, mencoba latihan praktik, menemukan bagian yang sempat salah, lalu memperbaikinya dan memastikan hasil akhirnya berjalan sesuai tujuan pembelajaran.';
    }
}
