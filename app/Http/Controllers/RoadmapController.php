<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\LearningMaterial;
use App\Models\ProgressLog;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Rules\GoogleDriveSubmissionFolder;
use App\Rules\GoogleDriveUrl;
use App\Services\AiInsightService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
            $item->status === 'locked',
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
                        'review_status' => $evaluation
                            ->review_status,
                        'evidence_url' => $evaluation
                            ->evidence_url,
                        'reviewed_at' => $evaluation
                            ->reviewed_at,
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

        $hasPendingSubmission = Evaluation::query()
            ->where(
                'user_id',
                $request
                    ->user()
                    ->id,
            )
            ->where(
                'roadmap_item_id',
                $currentItem->id,
            )
            ->where(
                'review_status',
                'pending',
            )
            ->exists();

        if ($hasPendingSubmission) {
            throw ValidationException::withMessages([
                'practical_evidence_url' => 'Pengumpulan sebelumnya masih diperiksa oleh admin. Tunggu sampai hasil pemeriksaan tersedia sebelum mengirim ulang.',
            ]);
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
                new GoogleDriveSubmissionFolder,
                'max:1000',
            ],
        ]);

        $submitted = DB::transaction(
            function () use (
                $request,
                $roadmapItem,
                $validated,
            ): bool {
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
                    return false;
                }

                $pendingExists = Evaluation::query()
                    ->where(
                        'user_id',
                        $request
                            ->user()
                            ->id,
                    )
                    ->where(
                        'roadmap_item_id',
                        $item->id,
                    )
                    ->where(
                        'review_status',
                        'pending',
                    )
                    ->exists();

                if ($pendingExists) {
                    throw ValidationException::withMessages([
                        'practical_evidence_url' => 'Pengumpulan sebelumnya masih diperiksa oleh admin. Tunggu sampai hasil pemeriksaan tersedia sebelum mengirim ulang.',
                    ]);
                }

                Evaluation::create([
                    'user_id' => $request
                        ->user()
                        ->id,
                    'roadmap_item_id' => $item->id,
                    'score' => 0,
                    'knowledge_score' => 0,
                    'evidence_score' => 0,
                    'reflection_score' => 0,
                    'passed' => false,
                    'answer' => $validated['answer'],
                    'evidence_url' => $validated[
                        'practical_evidence_url'
                    ],
                    'reflection' => null,
                    'feedback' => 'Pengumpulan sudah diterima dan sedang diperiksa oleh admin.',
                    'review_status' => 'pending',
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'admin_notes' => null,
                ]);

                $item->increment(
                    'evaluation_attempts',
                );

                ProgressLog::create([
                    'user_id' => $request
                        ->user()
                        ->id,
                    'roadmap_item_id' => $item->id,
                    'activity_type' => 'evaluation',
                    'minutes_spent' => 0,
                    'progress_percentage' => (int) $item
                        ->progress_percentage,
                    'notes' => 'Jawaban evaluasi dan bukti praktik dikirim untuk diperiksa oleh admin.',
                    'evidence_url' => $validated[
                        'practical_evidence_url'
                    ],
                    'logged_at' => now(),
                ]);

                return true;
            },
        );

        if (! $submitted) {
            return back()->with(
                'success',
                'Materi ini sudah selesai. Status penyelesaian tetap dipertahankan dan evaluasi tidak perlu diulang.',
            );
        }

        return back()->with(
            'success',
            'Pengumpulan berhasil dikirim. Statusnya sekarang sedang diperiksa oleh admin.',
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
