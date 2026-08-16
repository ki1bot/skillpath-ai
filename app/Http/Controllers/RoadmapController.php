<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\LearningMaterial;
use App\Models\ProgressLog;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Models\UserSkill;
use App\Rules\ExternalEvidenceUrl;
use App\Services\AdaptiveRoadmapService;
use App\Services\AiInsightService;
use App\Services\CareerReadinessService;
use App\Services\RoadmapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
                    'Selesaikan Assesment untuk membuat roadmap personal.',
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
                        'reflection_score' => $evaluation
                            ->reflection_score,
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
                new ExternalEvidenceUrl,
                'max:1000',
            ],
        ]);

        $roadmapItem->update([
            'progress_percentage' => max(
                $roadmapItem
                    ->progress_percentage,
                $validated[
                    'progress_percentage'
                ],
            ),
            'status' => (
                $roadmapItem->status
                === 'needs_reinforcement'
            )
                ? 'needs_reinforcement'
                : 'available',
        ]);

        ProgressLog::create([
            'user_id' => $request
                ->user()
                ->id,
            'roadmap_item_id' => $roadmapItem
                ->id,
            'activity_type' => 'learning',
            'minutes_spent' => $validated[
                'minutes_spent'
            ],
            'progress_percentage' => $validated[
                'progress_percentage'
            ],
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

        return back()->with(
            'success',
            'Progres belajar tersimpan. Materi baru dianggap dikuasai setelah evaluasi lulus.',
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

        $roadmapItem->load([
            'material.skill',
            'roadmap',
        ]);

        $validated = $request->validate([
            'answer' => [
                'required',
                'string',
                'in:A,B,C,D',
            ],
            'practical_evidence_url' => [
                'required',
                'string',
                new ExternalEvidenceUrl,
                'max:1000',
            ],
            'reflection' => [
                'required',
                'string',
                'min:80',
                'max:3000',
            ],
        ]);

        $material = $roadmapItem
            ->material;

        $correct = (
            $validated['answer']
            === $material->quiz_answer
        );

        $knowledgeScore = $correct
            ? 70
            : 0;

        $evidenceScore = 20;

        $reflection = trim(
            $validated['reflection'],
        );

        $reflectionScore = Str::length(
            $reflection,
        ) >= 80
            ? 10
            : 0;

        $score = round(
            min(
                $knowledgeScore
                + $evidenceScore
                + $reflectionScore,
                100,
            ),
            2,
        );

        $passed = $correct
            && $evidenceScore === 20
            && $reflectionScore === 10;

        if ($passed) {
            $feedback = (
                "Evaluasi lulus dengan skor {$score}/100. "
                ."Pemahaman konsep {$knowledgeScore}/70, "
                ."bukti praktik {$evidenceScore}/20, "
                ."dan refleksi {$reflectionScore}/10."
            );
        } else {
            $feedback = $material
                ->quiz_explanation
                ?: 'Jawaban konsep belum tepat. Pelajari kembali materi dan latihan praktik sebelum mencoba evaluasi lagi.';
        }

        Evaluation::create([
            'user_id' => $request
                ->user()
                ->id,
            'roadmap_item_id' => $roadmapItem
                ->id,
            'score' => $score,
            'knowledge_score' => $knowledgeScore,
            'evidence_score' => $evidenceScore,
            'reflection_score' => $reflectionScore,
            'passed' => $passed,
            'answer' => $validated[
                'answer'
            ],
            'evidence_url' => $validated[
                'practical_evidence_url'
            ],
            'reflection' => $reflection,
            'feedback' => $feedback,
        ]);

        $roadmapItem->increment(
            'evaluation_attempts',
        );

        $user = $request
            ->user()
            ->load('targetCareer');

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

            $roadmapItem->update([
                'status' => 'completed',
                'progress_percentage' => 100,
                'completed_at' => now(),
                'evaluation_score' => $score,
            ]);

            if ($isReinforcement) {
                $adaptiveRoadmapService
                    ->handlePassedReinforcement(
                        $user,
                        $roadmapItem->fresh([
                            'material',
                            'roadmap',
                        ]),
                    );
            }
        } else {
            $roadmapItem->update([
                'evaluation_score' => $score,
            ]);

            $reinforcementItem = $adaptiveRoadmapService
                ->handleFailedEvaluation(
                    $user,
                    $roadmapItem,
                );
        }

        ProgressLog::create([
            'user_id' => $user->id,
            'roadmap_item_id' => $roadmapItem
                ->id,
            'activity_type' => $passed
                ? 'evaluation_passed'
                : 'evaluation_failed',
            'minutes_spent' => 0,
            'progress_percentage' => $passed
                ? 100
                : $roadmapItem
                    ->fresh()
                    ->progress_percentage,
            'notes' => $feedback,
            'evidence_url' => $validated[
                'practical_evidence_url'
            ],
            'logged_at' => now(),
        ]);

        if ($passed) {
            $roadmapService->adaptAfterSkillChange(
                $user,
                "Roadmap diurutkan ulang setelah evaluasi {$material->title} mengubah skor {$material->skill->name}.",
            );
        } else {
            $roadmapService->refreshAvailability(
                $user,
            );
        }

        $readinessService->snapshot(
            $user->fresh(),
            $passed
                ? 'evaluation_passed'
                : 'evaluation_failed',
        );

        if (
            ! $passed
            && $reinforcementItem
        ) {
            return redirect()
                ->route('roadmap.index')
                ->with(
                    'error',
                    'Evaluasi belum lulus. Materi penguatan telah ditambahkan ke roadmap sebelum Anda mencoba materi ini kembali.',
                );
        }

        if (
            $passed
            && $material->material_type
                === 'reinforcement'
        ) {
            return redirect()
                ->route('roadmap.index')
                ->with(
                    'success',
                    'Materi penguatan berhasil diselesaikan. Materi utama sekarang dapat dicoba kembali dan urutan roadmap sudah disesuaikan dengan kemampuan terbaru.',
                );
        }

        return back()->with(
            $passed
                ? 'success'
                : 'error',
            $passed
                ? $feedback.' Urutan roadmap berikutnya sudah disesuaikan dengan kemampuan terbaru.'
                : $feedback,
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
