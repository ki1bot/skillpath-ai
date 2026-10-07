<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\ProgressLog;
use App\Models\RoadmapItem;
use App\Models\User;
use App\Models\UserSkill;
use App\Services\AdaptiveRoadmapService;
use App\Services\CareerReadinessService;
use App\Services\RoadmapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class EvaluationSubmissionController extends Controller
{
    public function index(Request $request): Response
    {
        $status = (string) $request->query(
            'status',
            'pending',
        );

        if (! in_array(
            $status,
            [
                'pending',
                'reviewed',
                'all',
            ],
            true,
        )) {
            $status = 'pending';
        }

        $query = Evaluation::query()
            ->with([
                'user:id,name,email,study_program',
                'roadmapItem.material.skill:id,name',
            ]);

        if ($status !== 'all') {
            $query->where(
                'review_status',
                $status,
            );
        }

        $submissions = $query
            ->orderByRaw(
                "CASE review_status
                    WHEN 'pending' THEN 0
                    WHEN 'reviewed' THEN 1
                    ELSE 2
                END",
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $reviewers = User::query()
            ->whereIn(
                'id',
                collect(
                    $submissions->items(),
                )
                    ->pluck('reviewed_by')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
            )
            ->get([
                'id',
                'name',
                'email',
            ])
            ->keyBy('id');

        $submissions->through(
            function (Evaluation $evaluation) use ($reviewers): array {
                $material = $evaluation
                    ->roadmapItem
                    ?->material;

                $reviewer = $evaluation->reviewed_by
                    ? $reviewers->get(
                        $evaluation->reviewed_by,
                    )
                    : null;

                return [
                    'id' => $evaluation->id,
                    'review_status' => $evaluation
                        ->review_status,
                    'score' => $evaluation->score,
                    'passed' => $evaluation->passed,
                    'evidence_url' => $evaluation
                        ->evidence_url,
                    'feedback' => $evaluation
                        ->feedback,
                    'admin_notes' => $evaluation
                        ->admin_notes,
                    'created_at' => $evaluation
                        ->created_at,
                    'reviewed_at' => $evaluation
                        ->reviewed_at,
                    'user' => [
                        'id' => $evaluation
                            ->user
                            ->id,
                        'name' => $evaluation
                            ->user
                            ->name,
                        'email' => $evaluation
                            ->user
                            ->email,
                        'study_program' => $evaluation
                            ->user
                            ->study_program,
                    ],
                    'reviewer' => $reviewer
                        ? [
                            'id' => $reviewer->id,
                            'name' => $reviewer->name,
                            'email' => $reviewer->email,
                        ]
                        : null,
                    'material' => $material
                        ? [
                            'title' => $material
                                ->title,
                            'practice_task' => $material
                                ->practice_task,
                            'skill' => $material
                                ->skill
                                ?->name,
                        ]
                        : null,
                ];
            },
        );

        return Inertia::render(
            'admin/submissions',
            [
                'status' => $status,
                'counts' => [
                    'pending' => Evaluation::query()
                        ->where(
                            'review_status',
                            'pending',
                        )
                        ->count(),
                    'reviewed' => Evaluation::query()
                        ->where(
                            'review_status',
                            'reviewed',
                        )
                        ->count(),
                    'passed' => Evaluation::query()
                        ->where(
                            'review_status',
                            'reviewed',
                        )
                        ->where(
                            'passed',
                            true,
                        )
                        ->count(),
                    'failed' => Evaluation::query()
                        ->where(
                            'review_status',
                            'reviewed',
                        )
                        ->where(
                            'passed',
                            false,
                        )
                        ->count(),
                ],
                'submissions' => $submissions,
            ],
        );
    }

    public function update(
        Request $request,
        Evaluation $evaluation,
        RoadmapService $roadmapService,
        AdaptiveRoadmapService $adaptiveRoadmapService,
        CareerReadinessService $readinessService,
    ): RedirectResponse {
        $validated = $request->validate([
            'score' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
            'admin_notes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ]);

        $score = (int) $validated['score'];
        $passed = $score > 70;

        $adminNotes = trim(
            (string) (
                $validated['admin_notes']
                ?? ''
            ),
        );

        $result = DB::transaction(
            function () use (
                $request,
                $evaluation,
                $score,
                $passed,
                $adminNotes,
                $adaptiveRoadmapService,
            ): array {
                $submission = Evaluation::query()
                    ->whereKey(
                        $evaluation->id,
                    )
                    ->with([
                        'user.targetCareer',
                        'roadmapItem.material.skill',
                        'roadmapItem.roadmap',
                    ])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $submission->review_status
                    !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'score' => 'Pengumpulan ini sudah diperiksa dan nilainya sudah ditetapkan.',
                    ]);
                }

                $item = RoadmapItem::query()
                    ->whereKey(
                        $submission
                            ->roadmap_item_id,
                    )
                    ->with([
                        'material.skill',
                        'roadmap',
                    ])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($item->status === 'completed') {
                    throw ValidationException::withMessages([
                        'score' => 'Materi ini sudah selesai dan tidak dapat dinilai ulang dari pengumpulan yang masih tertunda.',
                    ]);
                }

                $student = $submission
                    ->user;

                $material = $item
                    ->material;

                $feedback = $adminNotes !== ''
                    ? $adminNotes
                    : (
                        $passed
                            ? "Nilai {$score}/100. Tugas dinyatakan lulus."
                            : "Nilai {$score}/100. Tugas belum lulus karena nilai kelulusan harus di atas 70. Silakan perbaiki tugas sesuai hasil pemeriksaan admin."
                    );

                $submission->update([
                    'score' => $score,
                    'passed' => $passed,
                    'feedback' => $feedback,
                    'admin_notes' => $adminNotes !== ''
                        ? $adminNotes
                        : null,
                    'review_status' => 'reviewed',
                    'reviewed_by' => $request
                        ->user()
                        ->id,
                    'reviewed_at' => now(),
                ]);

                if ($passed) {
                    $current = UserSkill::firstOrNew([
                        'user_id' => $student->id,
                        'skill_id' => $material
                            ->skill_id,
                    ]);

                    $targetSkill = $student
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
                        'completed_at' => $item
                            ->completed_at
                            ?? now(),
                        'evaluation_score' => $score,
                    ]);

                    if ($isReinforcement) {
                        $adaptiveRoadmapService
                            ->handlePassedReinforcement(
                                $student,
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

                    $adaptiveRoadmapService
                        ->handleFailedEvaluation(
                            $student,
                            $item,
                        );
                }

                $effectiveProgress = $passed
                    ? 100
                    : (int) $item
                        ->fresh()
                        ->progress_percentage;

                ProgressLog::create([
                    'user_id' => $student->id,
                    'roadmap_item_id' => $item->id,
                    'activity_type' => $passed
                        ? 'evaluation_passed'
                        : 'evaluation_failed',
                    'minutes_spent' => 0,
                    'progress_percentage' => $effectiveProgress,
                    'notes' => $feedback,
                    'evidence_url' => $submission
                        ->evidence_url,
                    'logged_at' => now(),
                ]);

                return [
                    'user_id' => $student->id,
                    'passed' => $passed,
                    'score' => $score,
                    'material_title' => $material
                        ->title,
                    'skill_name' => $material
                        ->skill
                        ->name,
                ];
            },
        );

        $student = User::query()
            ->with('targetCareer')
            ->findOrFail(
                $result['user_id'],
            );

        if ($result['passed']) {
            $roadmapService->adaptAfterSkillChange(
                $student,
                "Roadmap diurutkan ulang setelah tugas {$result['material_title']} dinilai lulus dan mengubah skor {$result['skill_name']}.",
            );
        } else {
            $roadmapService->refreshAvailability(
                $student,
            );
        }

        $readinessService->snapshot(
            $student->fresh(),
            $result['passed']
                ? 'evaluation_passed'
                : 'evaluation_failed',
        );

        return back()->with(
            'success',
            $result['passed']
                ? "Nilai {$result['score']}/100 disimpan. Tugas dinyatakan lulus dan materi berikutnya dapat dibuka."
                : "Nilai {$result['score']}/100 disimpan. Tugas belum lulus karena nilai harus di atas 70.",
        );
    }
}
