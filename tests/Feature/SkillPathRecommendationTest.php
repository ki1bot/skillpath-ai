<?php

namespace Tests\Feature;

use App\Models\Evaluation;
use App\Models\LearningMaterial;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
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

    public function test_google_drive_submission_waits_for_admin_review(): void
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

        $this->submitEvaluation(
            $material,
            $item,
        );

        $item->refresh();

        $this->assertNotSame(
            'completed',
            $item->status,
        );

        $this->assertNull(
            $item->evaluation_score,
        );

        $this->assertSame(
            1,
            (int) $item->evaluation_attempts,
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

        $this->assertSame(
            'pending',
            $evaluation->review_status,
        );

        $this->assertFalse(
            $evaluation->passed,
        );

        $this->assertSame(
            0.0,
            (float) $evaluation->score,
        );

        $this->assertSame(
            0,
            $this->user
                ->evaluations()
                ->count(),
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
            ->value('score');

        $this->assertSame(
            $before,
            $after,
        );
    }

    public function test_pending_submission_cannot_be_sent_twice(): void
    {
        [$material, $item] = $this->databaseMaterialAndRoadmapItem();

        $this->submitEvaluation(
            $material,
            $item,
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
                    'answer' => $material
                        ->quiz_answer,
                    'practical_evidence_url' => 'https://drive.google.com/file/d/second-pending-submission/view',
                ],
            )
            ->assertSessionHasErrors([
                'practical_evidence_url',
            ]);

        $this->assertSame(
            1,
            Evaluation::query()
                ->where(
                    'roadmap_item_id',
                    $item->id,
                )
                ->count(),
        );
    }

    public function test_admin_can_see_question_answer_and_google_drive_submission(): void
    {
        [$material, $item] = $this->databaseMaterialAndRoadmapItem();

        $this->submitEvaluation(
            $material,
            $item,
        );

        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(
                route(
                    'admin.submissions.index',
                ),
            )
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component(
                        'admin/submissions',
                    )
                    ->where(
                        'submissions.data.0.material.quiz_question',
                        $material->quiz_question,
                    )
                    ->where(
                        'submissions.data.0.material.practice_task',
                        $material->practice_task,
                    )
                    ->where(
                        'submissions.data.0.answer',
                        $material->quiz_answer,
                    )
                    ->where(
                        'submissions.data.0.material.correct_answer',
                        $material->quiz_answer,
                    )
                    ->where(
                        'submissions.data.0.review_status',
                        'pending',
                    ),
            );
    }

    public function test_student_cannot_open_submission_management_page(): void
    {
        $this->actingAs(
            $this->user,
        )
            ->get(
                route(
                    'admin.submissions.index',
                ),
            )
            ->assertForbidden();
    }

    public function test_admin_score_70_passes_submission_and_completes_material(): void
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

        $evaluation = $this->submitEvaluation(
            $material,
            $item,
        );

        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(
                route(
                    'admin.submissions.update',
                    $evaluation,
                ),
                [
                    'score' => 70,
                    'admin_notes' => 'Pekerjaan sudah memenuhi batas kelulusan.',
                ],
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $evaluation->refresh();
        $item->refresh();

        $this->assertSame(
            'reviewed',
            $evaluation->review_status,
        );

        $this->assertTrue(
            $evaluation->passed,
        );

        $this->assertSame(
            70.0,
            (float) $evaluation->score,
        );

        $this->assertSame(
            $admin->id,
            $evaluation->reviewed_by,
        );

        $this->assertNotNull(
            $evaluation->reviewed_at,
        );

        $this->assertSame(
            'completed',
            $item->status,
        );

        $this->assertSame(
            100,
            (int) $item->progress_percentage,
        );

        $this->assertSame(
            70.0,
            (float) $item->evaluation_score,
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
            ->value('score');

        $this->assertGreaterThan(
            $before,
            $after,
        );
    }

    public function test_admin_score_69_fails_submission_without_increasing_skill_and_adds_reinforcement(): void
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
            ->value('score');

        $evaluation = $this->submitEvaluation(
            $material,
            $item,
        );

        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(
                route(
                    'admin.submissions.update',
                    $evaluation,
                ),
                [
                    'score' => 69,
                    'admin_notes' => 'Hasil praktik belum memenuhi batas kelulusan.',
                ],
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $evaluation->refresh();
        $item->refresh();

        $this->assertSame(
            'reviewed',
            $evaluation->review_status,
        );

        $this->assertFalse(
            $evaluation->passed,
        );

        $this->assertSame(
            69.0,
            (float) $evaluation->score,
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
            ->value('score');

        $this->assertSame(
            $before,
            $after,
        );

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

        $this->assertSame(
            1,
            (int) $item->evaluation_attempts,
        );
    }

    public function test_reviewed_submission_cannot_be_scored_twice(): void
    {
        [$material, $item] = $this->databaseMaterialAndRoadmapItem();

        $evaluation = $this->submitEvaluation(
            $material,
            $item,
        );

        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(
                route(
                    'admin.submissions.update',
                    $evaluation,
                ),
                [
                    'score' => 80,
                ],
            )
            ->assertSessionHasNoErrors();

        $this->actingAs($admin)
            ->patch(
                route(
                    'admin.submissions.update',
                    $evaluation,
                ),
                [
                    'score' => 40,
                ],
            )
            ->assertSessionHasErrors([
                'score',
            ]);

        $evaluation->refresh();

        $this->assertSame(
            80.0,
            (float) $evaluation->score,
        );

        $this->assertTrue(
            $evaluation->passed,
        );
    }

    public function test_completed_roadmap_item_cannot_be_downgraded_by_progress_update(): void
    {
        [$material, $item] = $this->databaseMaterialAndRoadmapItem();

        $evaluation = $this->submitEvaluation(
            $material,
            $item,
        );

        $this->actingAs(
            $this->admin(),
        )
            ->patch(
                route(
                    'admin.submissions.update',
                    $evaluation,
                ),
                [
                    'score' => 90,
                ],
            )
            ->assertSessionHasNoErrors();

        $item->refresh();

        $this->assertSame(
            'completed',
            $item->status,
        );

        $this->assertSame(
            100,
            (int) $item->progress_percentage,
        );

        $completedAt = $item
            ->completed_at
            ?->toISOString();

        $evaluationScore = (float) $item
            ->evaluation_score;

        $this->actingAs(
            $this->user,
        )
            ->patch(
                route(
                    'roadmap.progress',
                    $item,
                ),
                [
                    'progress_percentage' => 20,
                    'minutes_spent' => 5,
                    'notes' => 'Meninjau ulang materi.',
                    'obstacle' => null,
                    'evidence_url' => null,
                ],
            )
            ->assertSessionHasNoErrors();

        $item->refresh();

        $this->assertSame(
            'completed',
            $item->status,
        );

        $this->assertSame(
            100,
            (int) $item->progress_percentage,
        );

        $this->assertSame(
            $completedAt,
            $item
                ->completed_at
                ?->toISOString(),
        );

        $this->assertSame(
            $evaluationScore,
            (float) $item
                ->evaluation_score,
        );
    }

    public function test_completed_roadmap_item_cannot_be_submitted_again(): void
    {
        [$material, $item] = $this->databaseMaterialAndRoadmapItem();

        $evaluation = $this->submitEvaluation(
            $material,
            $item,
        );

        $this->actingAs(
            $this->admin(),
        )
            ->patch(
                route(
                    'admin.submissions.update',
                    $evaluation,
                ),
                [
                    'score' => 90,
                ],
            )
            ->assertSessionHasNoErrors();

        $item->refresh();

        $completedAt = $item
            ->completed_at
            ?->toISOString();

        $evaluationCount = Evaluation::query()
            ->where(
                'roadmap_item_id',
                $item->id,
            )
            ->count();

        $attemptCount = (int) $item
            ->evaluation_attempts;

        $evaluationScore = (float) $item
            ->evaluation_score;

        $skillScore = (float) UserSkill::query()
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
                    'practical_evidence_url' => 'https://drive.google.com/file/d/completed-second/view',
                ],
            )
            ->assertSessionHasNoErrors();

        $item->refresh();

        $this->assertSame(
            'completed',
            $item->status,
        );

        $this->assertSame(
            100,
            (int) $item->progress_percentage,
        );

        $this->assertSame(
            $completedAt,
            $item
                ->completed_at
                ?->toISOString(),
        );

        $this->assertSame(
            $attemptCount,
            (int) $item
                ->evaluation_attempts,
        );

        $this->assertSame(
            $evaluationScore,
            (float) $item
                ->evaluation_score,
        );

        $this->assertSame(
            $evaluationCount,
            Evaluation::query()
                ->where(
                    'roadmap_item_id',
                    $item->id,
                )
                ->count(),
        );

        $this->assertSame(
            $skillScore,
            (float) UserSkill::query()
                ->where(
                    'user_id',
                    $this->user->id,
                )
                ->where(
                    'skill_id',
                    $material->skill_id,
                )
                ->value('score'),
        );

        $this->assertFalse(
            RoadmapItem::query()
                ->where(
                    'roadmap_id',
                    $item->roadmap_id,
                )
                ->where(
                    'reinforcement_for_roadmap_item_id',
                    $item->id,
                )
                ->exists(),
        );
    }

    private function submitEvaluation(
        LearningMaterial $material,
        RoadmapItem $item,
    ): Evaluation {
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
                    'practical_evidence_url' => 'https://drive.google.com/drive/folders/skillpath-evidence',
                ],
            )
            ->assertSessionHasNoErrors();

        return Evaluation::query()
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
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
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
