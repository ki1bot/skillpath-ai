<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgressLog;
use App\Models\User;
use App\Models\UserProject;
use App\Services\CareerReadinessService;
use App\Support\SkillPathScoringPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ProjectSubmissionController extends Controller
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

        $query = UserProject::query()
            ->whereIn(
                'review_status',
                [
                    'pending',
                    'reviewed',
                ],
            )
            ->with([
                'user:id,name,email,study_program',
                'project.career:id,name',
                'project.skills:id,name',
                'reviewer:id,name,email',
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
            ->latest('submitted_at')
            ->paginate(20)
            ->withQueryString();

        $submissions->through(
            function (UserProject $submission): array {
                return [
                    'id' => $submission->id,
                    'status' => $submission->status,
                    'review_status' => $submission
                        ->review_status,
                    'evaluation_score' => $submission
                        ->evaluation_score,
                    'repository_url' => $submission
                        ->repository_url,
                    'admin_notes' => $submission
                        ->admin_notes,
                    'submitted_at' => $submission
                        ->submitted_at,
                    'reviewed_at' => $submission
                        ->reviewed_at,
                    'user' => [
                        'id' => $submission
                            ->user
                            ->id,
                        'name' => $submission
                            ->user
                            ->name,
                        'email' => $submission
                            ->user
                            ->email,
                        'study_program' => $submission
                            ->user
                            ->study_program,
                    ],
                    'reviewer' => $submission->reviewer
                        ? [
                            'id' => $submission
                                ->reviewer
                                ->id,
                            'name' => $submission
                                ->reviewer
                                ->name,
                            'email' => $submission
                                ->reviewer
                                ->email,
                        ]
                        : null,
                    'project' => [
                        'id' => $submission
                            ->project
                            ->id,
                        'title' => $submission
                            ->project
                            ->title,
                        'summary' => $submission
                            ->project
                            ->summary,
                        'problem_statement' => $submission
                            ->project
                            ->problem_statement,
                        'minimum_features' => $submission
                            ->project
                            ->minimum_features,
                        'completion_criteria' => $submission
                            ->project
                            ->completion_criteria,
                        'career' => $submission
                            ->project
                            ->career
                            ->name,
                        'skills' => $submission
                            ->project
                            ->skills
                            ->pluck('name')
                            ->values(),
                    ],
                ];
            },
        );

        return Inertia::render(
            'admin/project-submissions',
            [
                'status' => $status,
                'counts' => [
                    'pending' => UserProject::query()
                        ->where(
                            'review_status',
                            'pending',
                        )
                        ->count(),
                    'reviewed' => UserProject::query()
                        ->where(
                            'review_status',
                            'reviewed',
                        )
                        ->count(),
                    'passed' => UserProject::query()
                        ->where(
                            'review_status',
                            'reviewed',
                        )
                        ->where(
                            'evaluation_score',
                            '>=',
                            SkillPathScoringPolicy::PROJECT_PASS_SCORE,
                        )
                        ->count(),
                    'failed' => UserProject::query()
                        ->where(
                            'review_status',
                            'reviewed',
                        )
                        ->whereNotNull(
                            'evaluation_score',
                        )
                        ->where(
                            'evaluation_score',
                            '<',
                            SkillPathScoringPolicy::PROJECT_PASS_SCORE,
                        )
                        ->count(),
                ],
                'submissions' => $submissions,
            ],
        );
    }

    public function update(
        Request $request,
        UserProject $userProject,
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

        $passed = $score
            >= SkillPathScoringPolicy::PROJECT_PASS_SCORE;

        $adminNotes = trim(
            (string) (
                $validated['admin_notes']
                ?? ''
            ),
        );

        $result = DB::transaction(
            function () use (
                $request,
                $userProject,
                $score,
                $passed,
                $adminNotes,
            ): array {
                $submission = UserProject::query()
                    ->whereKey(
                        $userProject->id,
                    )
                    ->with([
                        'user',
                        'project',
                    ])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $submission->review_status
                    !== 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'score' => 'Pengumpulan proyek ini sudah diperiksa.',
                    ]);
                }

                $feedback = $adminNotes !== ''
                    ? $adminNotes
                    : (
                        $passed
                            ? "Nilai {$score}/100. Proyek memenuhi nilai kelulusan."
                            : "Nilai {$score}/100. Proyek belum mencapai nilai minimal 80. Perbaiki bagian yang masih belum memenuhi kriteria."
                    );

                $submission->update([
                    'evaluation_score' => $score,
                    'review_status' => 'reviewed',
                    'status' => $passed
                        ? 'completed'
                        : 'needs_revision',
                    'progress_percentage' => $passed
                        ? 100
                        : 95,
                    'reviewed_by' => $request
                        ->user()
                        ->id,
                    'reviewed_at' => now(),
                    'admin_notes' => $feedback,
                    'completed_at' => $passed
                        ? now()
                        : null,
                ]);

                ProgressLog::create([
                    'user_id' => $submission
                        ->user_id,
                    'activity_type' => $passed
                        ? 'project_completed'
                        : 'project_progress',
                    'minutes_spent' => 0,
                    'progress_percentage' => $passed
                        ? 100
                        : 95,
                    'notes' => $feedback,
                    'evidence_url' => $submission
                        ->repository_url,
                    'logged_at' => now(),
                ]);

                return [
                    'user_id' => $submission
                        ->user_id,
                    'score' => $score,
                    'passed' => $passed,
                    'project_title' => $submission
                        ->project
                        ->title,
                ];
            },
        );

        $student = User::query()
            ->findOrFail(
                $result['user_id'],
            );

        $readinessService->snapshot(
            $student,
            $result['passed']
                ? 'project_completed'
                : 'project_progress',
        );

        return back()->with(
            'success',
            $result['passed']
                ? "Nilai {$result['score']}/100 disimpan. Proyek {$result['project_title']} dinyatakan lulus."
                : "Nilai {$result['score']}/100 disimpan. Proyek belum mencapai nilai minimal 80 dan perlu diperbaiki.",
        );
    }
}
