<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\LearningMaterial;
use App\Models\ProgressLog;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Models\UserSkill;
use App\Rules\GoogleDriveUrl;
use App\Services\AdaptiveRoadmapService;
use App\Services\AiInsightService;
use App\Services\CareerReadinessService;
use App\Services\RoadmapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RoadmapController extends Controller
{
    public function index(
        Request $request,
    ): Response|RedirectResponse {
        $user = $request
            ->user()
            ->load('targetCareer');

        $roadmap = Roadmap::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->with([
                'career',
                'items.material.skill.prerequisites',
            ])
            ->first();

        if (! $roadmap) {
            return redirect()
                ->route('assessment.show')
                ->with(
                    'error',
                    'Selesaikan Assessment untuk membuat roadmap personal.',
                );
        }

        $payload = [
            'id' => $roadmap->id,
            'version' => $roadmap->version,
            'reason' => $roadmap->reason,
            'estimated_weeks' => $roadmap->estimated_weeks,
            'career' => [
                'name' => $roadmap
                    ->career
                    ->name,
            ],
            'items' => $roadmap
                ->items
                ->map(
                    fn ($item) => [
                        'id' => $item->id,
                        'stage' => $item->stage,
                        'stage_title' => $item->stage_title,
                        'position' => $item->position,
                        'status' => $item->status,
                        'progress_percentage' => $item
                            ->progress_percentage,
                        'evaluation_score' => $item
                            ->evaluation_score,
                        'evaluation_attempts' => $item
                            ->evaluation_attempts,
                        'reinforcement_count' => $item
                            ->reinforcement_count,
                        'reinforcement_for_roadmap_item_id' => $item
                            ->reinforcement_for_roadmap_item_id,
                        'material' => [
                            'id' => $item
                                ->material
                                ->id,
                            'title' => $item
                                ->material
                                ->title,
                            'slug' => $item
                                ->material
                                ->slug,
                            'summary' => $item
                                ->material
                                ->summary,
                            'difficulty' => $item
                                ->material
                                ->difficulty,
                            'estimated_minutes' => $item
                                ->material
                                ->estimated_minutes,
                            'material_type' => $item
                                ->material
                                ->material_type,
                            'skill' => [
                                'id' => $item
                                    ->material
                                    ->skill
                                    ->id,
                                'name' => $item
                                    ->material
                                    ->skill
                                    ->name,
                                'prerequisites' => $item
                                    ->material
                                    ->skill
                                    ->prerequisites
                                    ->map(
                                        fn ($prerequisite) => [
                                            'id' => $prerequisite->id,
                                            'name' => $prerequisite->name,
                                        ],
                                    )
                                    ->values(),
                            ],
                        ],
                    ],
                )
                ->values(),
        ];

        return Inertia::render(
            'roadmap',
            [
                'roadmap' => $payload,
            ],
        );
    }

    public function material(
        Request $request,
        LearningMaterial $material,
        AiInsightService $aiInsightService,
    ): Response {
        $item = RoadmapItem::query()
            ->where(
                'learning_material_id',
                $material->id,
            )
            ->whereHas(
                'roadmap',
                fn ($query) => $query
                    ->where(
                        'user_id',
                        $request
                            ->user()
                            ->id,
                    )
                    ->where(
                        'is_active',
                        true,
                    ),
            )
            ->with([
                'material.skill.prerequisites',
                'evaluations' => fn ($query) => $query
                    ->latest(),
            ])
            ->firstOrFail();

        abort_if(
            in_array(
                $item->status,
                [
                    'locked',
                    'reinforcement_required',
                ],
                true,
            ),
            403,
            'Materi ini masih terkunci.',
        );

        $materialPayload = [
            'id' => $item
                ->material
                ->id,
            'title' => $item
                ->material
                ->title,
            'slug' => $item
                ->material
                ->slug,
            'summary' => $item
                ->material
                ->summary,
            'learning_objectives' => $item
                ->material
                ->learning_objectives,
            'difficulty' => $item
                ->material
                ->difficulty,
            'estimated_minutes' => $item
                ->material
                ->estimated_minutes,
            'resource_title' => $item
                ->material
                ->resource_title,
            'resource_url' => $item
                ->material
                ->resource_url,
            'practice_task' => $item
                ->material
                ->practice_task,
            'quiz_question' => $item
                ->material
                ->quiz_question,
            'quiz_options' => $item
                ->material
                ->quiz_options,
            'material_type' => $item
                ->material
                ->material_type,
            'skill' => [
                'name' => $item
                    ->material
                    ->skill
                    ->name,
                'prerequisites' => $item
                    ->material
                    ->skill
                    ->prerequisites
                    ->map(
                        fn ($prerequisite) => [
                            'id' => $prerequisite->id,
                            'name' => $prerequisite->name,
                        ],
                    )
                    ->values(),
            ],
        ];

        $itemPayload = [
            'id' => $item->id,
            'status' => $item->status,
            'progress_percentage' => $item
                ->progress_percentage,
            'evaluation_score' => $item
                ->evaluation_score,
            'evaluation_attempts' => $item
                ->evaluation_attempts,
            'reinforcement_count' => $item
                ->reinforcement_count,
            'evaluations' => $item
                ->evaluations
                ->map(
                    fn ($evaluation) => [
                        'id' => $evaluation->id,
                        'score' => $evaluation->score,
                        'knowledge_score' => $evaluation
                            ->knowledge_score,
                        'evidence_score' => $evaluation
                            ->evidence_score,
                        'passed' => $evaluation
                            ->passed,
                        'feedback' => $evaluation
                            ->feedback,
                        'created_at' => $evaluation
                            ->created_at,
                    ],
                )
                ->values(),
        ];

        $user = $request->user();

        return Inertia::render(
            'material',
            [
                'item' => $itemPayload,
                'material' => $materialPayload,
                'aiExercise' => Inertia::defer(
                    function () use (
                        $aiInsightService,
                        $item,
                        $user,
                    ): array {
                        $aiExercise = $aiInsightService
                            ->exerciseVariation(
                                $user,
                                $item->material,
                            );

                        return [
                            'content' => $aiExercise['content'],
                            'generatedByAi' => $aiExercise['generated_by_ai'],
                            'model' => $aiExercise['model'],
                            'message' => $aiExercise['generated_by_ai']
                                ? null
                                : 'Variasi latihan AI sedang tidak tersedia. Silakan coba lagi.',
                        ];
                    },
                ),
            ],
        );
    }

    public function logProgress(
        Request $request,
        RoadmapItem $roadmapItem,
    ): RedirectResponse {
        $this->authorizeItem(
            $request,
            $roadmapItem,
        );

        $validated = $request->validate([
            'progress_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:95',
            ],
            'minutes_spent' => [
                'required',
                'integer',
                'min:0',
                'max:1440',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'obstacle' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'evidence_url' => [
                'nullable',
                'string',
                new GoogleDriveUrl,
                'max:1000',
            ],
        ]);

        $result = DB::transaction(
            function () use (
                $request,
                $roadmapItem,
                $validated,
            ): array {
                $item = RoadmapItem::query()
                    ->whereKey(
                        $roadmapItem->id,
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->authorizeItem(
                    $request,
                    $item,
                );

                if ($item->status === 'completed') {
                    $effectiveProgress = 100;
                    $effectiveStatus = 'completed';
                } else {
                    $effectiveProgress = max(
                        (int) $item->progress_percentage,
                        (int) $validated[
                            'progress_percentage'
                        ],
                    );

                    $effectiveStatus = $item->status
                        === 'needs_reinforcement'
                        ? 'needs_reinforcement'
                        : 'available';
                }

                $item->update([
                    'progress_percentage' => $effectiveProgress,
                    'status' => $effectiveStatus,
                ]);

                ProgressLog::create([
                    'user_id' => $request
                        ->user()
                        ->id,
                    'roadmap_item_id' => $item->id,
                    'activity_type' => 'learning',
                    'minutes_spent' => $validated[
                        'minutes_spent'
                    ],
                    'progress_percentage' => $effectiveProgress,
                    'notes' => $validated[
                        'notes'
                    ] ?? null,
                    'obstacle' => $validated[
                        'obstacle'
                    ] ?? null,
                    'evidence_url' => $validated[
                        'evidence_url'
                    ] ?? null,
                    'logged_at' => now(),
                ]);

                return [
                    'status' => $effectiveStatus,
                    'progress_percentage' => $effectiveProgress,
                ];
            },
        );

        return back()->with(
            'success',
            $result['status'] === 'completed'
                ? 'Aktivitas belajar tersimpan. Status materi tetap selesai.'
                : 'Progres belajar tersimpan. Materi baru dianggap dikuasai setelah evaluasi lulus.',
        );
    }

    public function evaluate(
        Request $request,
        RoadmapItem $roadmapItem,
        RoadmapService $roadmapService,
        AdaptiveRoadmapService $adaptiveRoadmapService,
        CareerReadinessService $readinessService,
    ): RedirectResponse {
        $this->authorizeItem(
            $request,
            $roadmapItem,
        );

        $currentItem = RoadmapItem::query()
            ->whereKey(
                $roadmapItem->id,
            )
            ->firstOrFail();

        if ($currentItem->status === 'completed') {
            return back()->with(
                'success',
                'Materi ini sudah selesai. Status penyelesaian tetap dipertahankan dan evaluasi tidak perlu diulang.',
            );
        }

        $validated = $request->validate([
            'answer' => [
                'required',
                'string',
                'in:A,B,C,D',
            ],
            'practical_evidence_url' => [
                'required',
                'string',
                new GoogleDriveUrl,
                'max:1000',
            ],
        ]);

        $user = $request
            ->user()
            ->load('targetCareer');

        $result = DB::transaction(
            function () use (
                $request,
                $roadmapItem,
                $validated,
                $user,
                $adaptiveRoadmapService,
            ): array {
                $item = RoadmapItem::query()
                    ->whereKey(
                        $roadmapItem->id,
                    )
                    ->with([
                        'material.skill',
                        'roadmap',
                    ])
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->authorizeItem(
                    $request,
                    $item,
                );

                if ($item->status === 'completed') {
                    return [
                        'already_completed' => true,
                    ];
                }

                $material = $item
                    ->material;

                $correct = (
                    $validated['answer']
                    === $material->quiz_answer
                );

                $knowledgeScore = $correct
                    ? 80
                    : 0;

                $evidenceScore = 20;

                $score = round(
                    min(
                        $knowledgeScore
                        + $evidenceScore,
                        100,
                    ),
                    2,
                );

                $passed = $correct;

                if ($passed) {
                    $feedback = (
                        "Evaluasi lulus dengan skor {$score}/100. "
                        ."Pemahaman konsep {$knowledgeScore}/80 dan "
                        ."bukti praktik {$evidenceScore}/20."
                    );
                } else {
                    $feedback = $material
                        ->quiz_explanation
                        ?: 'Jawaban konsep belum tepat. Pelajari kembali materi dan latihan praktik sebelum mencoba evaluasi lagi.';
                }

                Evaluation::create([
                    'user_id' => $user->id,
                    'roadmap_item_id' => $item
                        ->id,
                    'score' => $score,
                    'knowledge_score' => $knowledgeScore,
                    'evidence_score' => $evidenceScore,
                    'reflection_score' => 0,
                    'passed' => $passed,
                    'answer' => $validated[
                        'answer'
                    ],
                    'evidence_url' => $validated[
                        'practical_evidence_url'
                    ],
                    'reflection' => null,
                    'feedback' => $feedback,
                ]);

                $item->increment(
                    'evaluation_attempts',
                );

                $reinforcementItem = null;

                if ($passed) {
                    $current = UserSkill::firstOrNew([
                        'user_id' => $user->id,
                        'skill_id' => $material
                            ->skill_id,
                    ]);

                    $targetSkill = $user
                        ->targetCareer
                        ?->skills()
                        ->where(
                            'skills.id',
                            $material->skill_id,
                        )
                        ->first();

                    $target = (float) (
                        $targetSkill
                            ?->pivot
                            ->target_level
                        ?? 80
                    );

                    $currentScore = (float) (
                        $current->score
                        ?? 0
                    );

                    $isReinforcement = (
                        $material->material_type
                        === 'reinforcement'
                    );

                    $increment = $isReinforcement
                        ? 10
                        : 20;

                    $minimumTarget = $isReinforcement
                        ? $target * 0.70
                        : $target * 0.80;

                    $newScore = min(
                        100,
                        max(
                            $currentScore
                            + $increment,
                            $minimumTarget,
                        ),
                    );

                    $current->fill([
                        'score' => round(
                            $newScore,
                            2,
                        ),
                        'source' => 'evaluation',
                        'last_assessed_at' => now(),
                    ])->save();

                    $item->update([
                        'status' => 'completed',
                        'progress_percentage' => 100,
                        'completed_at' => $item->completed_at
                            ?? now(),
                        'evaluation_score' => $score,
                    ]);

                    if ($isReinforcement) {
                        $adaptiveRoadmapService
                            ->handlePassedReinforcement(
                                $user,
                                $item->fresh([
                                    'material',
                                    'roadmap',
                                ]),
                            );
                    }
                } else {
                    $item->update([
                        'evaluation_score' => $score,
                    ]);

                    $reinforcementItem = $adaptiveRoadmapService
                        ->handleFailedEvaluation(
                            $user,
                            $item,
                        );
                }

                $effectiveProgress = $passed
                    ? 100
                    : (int) $item
                        ->fresh()
                        ->progress_percentage;

                ProgressLog::create([
                    'user_id' => $user->id,
                    'roadmap_item_id' => $item
                        ->id,
                    'activity_type' => $passed
                        ? 'evaluation_passed'
                        : 'evaluation_failed',
                    'minutes_spent' => 0,
                    'progress_percentage' => $effectiveProgress,
                    'notes' => $feedback,
                    'evidence_url' => $validated[
                        'practical_evidence_url'
                    ],
                    'logged_at' => now(),
                ]);

                return [
                    'already_completed' => false,
                    'passed' => $passed,
                    'feedback' => $feedback,
                    'reinforcement_item_id' => $reinforcementItem
                        ?->id,
                    'is_reinforcement' => (
                        $material->material_type
                        === 'reinforcement'
                    ),
                    'material_title' => $material
                        ->title,
                    'skill_name' => $material
                        ->skill
                        ->name,
                ];
            },
        );

        if ($result['already_completed']) {
            return back()->with(
                'success',
                'Materi ini sudah selesai. Status penyelesaian tetap dipertahankan dan evaluasi tidak perlu diulang.',
            );
        }

        if ($result['passed']) {
            $roadmapService->adaptAfterSkillChange(
                $user,
                "Roadmap diurutkan ulang setelah evaluasi {$result['material_title']} mengubah skor {$result['skill_name']}.",
            );
        } else {
            $roadmapService->refreshAvailability(
                $user,
            );
        }

        $readinessService->snapshot(
            $user->fresh(),
            $result['passed']
                ? 'evaluation_passed'
                : 'evaluation_failed',
        );

        if (
            ! $result['passed']
            && $result['reinforcement_item_id']
        ) {
            return redirect()
                ->route('roadmap.index')
                ->with(
                    'error',
                    'Evaluasi belum lulus. Materi penguatan telah ditambahkan ke roadmap sebelum Anda mencoba materi ini kembali.',
                );
        }

        if (
            $result['passed']
            && $result['is_reinforcement']
        ) {
            return redirect()
                ->route('roadmap.index')
                ->with(
                    'success',
                    'Materi penguatan berhasil diselesaikan. Materi utama sekarang dapat dicoba kembali dan urutan roadmap sudah disesuaikan dengan kemampuan terbaru.',
                );
        }

        return back()->with(
            $result['passed']
                ? 'success'
                : 'error',
            $result['passed']
                ? $result['feedback'].' Urutan roadmap berikutnya sudah disesuaikan dengan kemampuan terbaru.'
                : $result['feedback'],
        );
    }

    private function authorizeItem(
        Request $request,
        RoadmapItem $item,
    ): void {
        $owned = $item
            ->roadmap()
            ->where(
                'user_id',
                $request
                    ->user()
                    ->id,
            )
            ->where(
                'is_active',
                true,
            )
            ->exists();

        abort_unless(
            $owned,
            403,
        );

        abort_if(
            in_array(
                $item->status,
                [
                    'locked',
                    'reinforcement_required',
                ],
                true,
            ),
            403,
            'Materi ini masih terkunci.',
        );
    }
}
