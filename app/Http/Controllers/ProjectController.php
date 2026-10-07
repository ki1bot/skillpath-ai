<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;
use App\Models\ProgressLog;
use App\Models\UserProject;
use App\Rules\GoogleDriveSubmissionFolder;
use App\Services\AiInsightService;
use App\Services\ProjectReadinessService;
use App\Support\AcademicProgramCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(
        Request $request,
        ProjectReadinessService $service,
    ): Response|RedirectResponse {
        $user = $request
            ->user()
            ->loadMissing(
                'targetCareer',
            );

        if (! $user->target_career_id) {
            return redirect()->route(
                'onboarding.show',
            );
        }

        $studyProgram = (string) $user
            ->targetCareer
            ->name;

        $projects = PortfolioProject::query()
            ->where(
                'career_id',
                $user->target_career_id,
            )
            ->with(
                'skills',
            )
            ->get()
            ->map(
                function (
                    PortfolioProject $project,
                ) use (
                    $user,
                    $service,
                    $studyProgram,
                ): array {
                    $area = $this->projectArea(
                        $studyProgram,
                        $project,
                    );

                    return [
                        ...$project->toArray(),
                        'project_number' => $area[
                            'number'
                        ],
                        'area_name' => $area[
                            'name'
                        ],
                        'readiness' => $service
                            ->calculate(
                                $user,
                                $project,
                            ),
                        'user_project' => UserProject::query()
                            ->where(
                                'user_id',
                                $user->id,
                            )
                            ->where(
                                'portfolio_project_id',
                                $project->id,
                            )
                            ->first(),
                    ];
                },
            )
            ->sortBy(
                'project_number',
            )
            ->values();

        return Inertia::render(
            'projects',
            [
                'projects' => $projects,
            ],
        );
    }

    public function show(
        Request $request,
        PortfolioProject $portfolioProject,
        ProjectReadinessService $service,
        AiInsightService $aiInsightService,
    ): Response {
        $user = $request->user();

        abort_unless(
            $portfolioProject->career_id
            === $user->target_career_id,
            404,
        );

        $portfolioProject->load([
            'career',
            'skills',
        ]);

        $userProject = UserProject::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->where(
                'portfolio_project_id',
                $portfolioProject->id,
            )
            ->first();

        $readiness = $service->calculate(
            $user,
            $portfolioProject,
        );

        return Inertia::render(
            'project-show',
            [
                'project' => $portfolioProject,
                'readiness' => $readiness,
                'userProject' => $userProject,
                'aiFeedback' => Inertia::defer(
                    function () use (
                        $aiInsightService,
                        $user,
                        $portfolioProject,
                        $userProject,
                        $readiness,
                    ): array {
                        $aiFeedback = $aiInsightService
                            ->projectFeedback(
                                $user,
                                $portfolioProject,
                                $userProject,
                                $readiness,
                            );

                        $generated = $aiFeedback[
                            'generated_by_ai'
                        ]
                            && is_string(
                                $aiFeedback[
                                    'content'
                                ],
                            )
                            && trim(
                                $aiFeedback[
                                    'content'
                                ],
                            ) !== '';

                        return [
                            'content' => $aiFeedback[
                                'content'
                            ],
                            'generatedByAi' => $generated,
                            'model' => $aiFeedback[
                                'model'
                            ],
                            'message' => $generated
                                ? null
                                : 'Penjelasan proyek dari AI sedang tidak tersedia. Silakan coba lagi.',
                        ];
                    },
                ),
            ],
        );
    }

    public function start(
        Request $request,
        PortfolioProject $portfolioProject,
        ProjectReadinessService $readinessService,
    ): RedirectResponse {
        $user = $request->user();

        abort_unless(
            $portfolioProject->career_id
            === $user->target_career_id,
            404,
        );

        $portfolioProject->loadMissing(
            'skills',
        );

        $readiness = $readinessService->calculate(
            $user,
            $portfolioProject,
        );

        $userProject = UserProject::firstOrCreate(
            [
                'user_id' => $user->id,
                'portfolio_project_id' => $portfolioProject->id,
            ],
            [
                'status' => 'in_progress',
                'progress_percentage' => 0,
                'review_status' => 'not_submitted',
                'started_at' => now(),
            ],
        );

        if (! $userProject->wasRecentlyCreated) {
            return back()->with(
                'success',
                'Proyek ini sudah pernah dimulai. Lanjutkan pengerjaan dan kirim folder Google Drive setelah seluruh bagian wajib selesai.',
            );
        }

        ProgressLog::create([
            'user_id' => $user->id,
            'activity_type' => 'project_started',
            'minutes_spent' => 0,
            'progress_percentage' => 0,
            'notes' => 'Proyek dimulai dengan status rekomendasi: '
                .$readiness[
                    'recommendation'
                ][
                    'label'
                ]
                .'.',
            'logged_at' => now(),
        ]);

        return back()->with(
            'success',
            $readiness[
                'recommendation'
            ][
                'level'
            ] === 'challenge'
                ? 'Proyek dimulai sebagai tantangan. Kerjakan bagian wajib secara bertahap dan gunakan daftar kemampuan sebagai panduan.'
                : 'Proyek dimulai. Kerjakan seluruh bagian wajib sebelum mengirim hasil untuk diperiksa admin.',
        );
    }

    public function update(
        Request $request,
        PortfolioProject $portfolioProject,
    ): RedirectResponse {
        $user = $request->user();

        abort_unless(
            $portfolioProject->career_id
            === $user->target_career_id,
            404,
        );

        $current = UserProject::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->where(
                'portfolio_project_id',
                $portfolioProject->id,
            )
            ->firstOrFail();

        if (
            $current->status
            === 'completed'
        ) {
            throw ValidationException::withMessages([
                'repository_url' => 'Proyek ini sudah lulus dan tidak perlu dikumpulkan ulang.',
            ]);
        }

        if (
            $current->review_status
            === 'pending'
        ) {
            throw ValidationException::withMessages([
                'repository_url' => 'Pengumpulan proyek sebelumnya masih diperiksa oleh admin.',
            ]);
        }

        $validated = $request->validate([
            'repository_url' => [
                'required',
                'string',
                new GoogleDriveSubmissionFolder,
                'max:1000',
            ],
        ]);

        $googleDriveUrl = trim(
            (string) $validated[
                'repository_url'
            ],
        );

        DB::transaction(
            function () use (
                $user,
                $current,
                $googleDriveUrl,
            ): void {
                $userProject = UserProject::query()
                    ->whereKey(
                        $current->id,
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $userProject->status
                    === 'completed'
                ) {
                    throw ValidationException::withMessages([
                        'repository_url' => 'Proyek ini sudah lulus dan tidak perlu dikumpulkan ulang.',
                    ]);
                }

                if (
                    $userProject->review_status
                    === 'pending'
                ) {
                    throw ValidationException::withMessages([
                        'repository_url' => 'Pengumpulan proyek sebelumnya masih diperiksa oleh admin.',
                    ]);
                }

                $userProject->update([
                    'status' => 'submitted',
                    'progress_percentage' => 95,
                    'review_status' => 'pending',
                    'repository_url' => $googleDriveUrl,
                    'submitted_at' => now(),
                    'completed_at' => null,
                ]);

                ProgressLog::create([
                    'user_id' => $user->id,
                    'activity_type' => 'project_progress',
                    'minutes_spent' => 0,
                    'progress_percentage' => 95,
                    'notes' => 'Hasil proyek dikirim melalui Google Drive dan sedang menunggu pemeriksaan admin.',
                    'evidence_url' => $googleDriveUrl,
                    'logged_at' => now(),
                ]);
            },
        );

        return back()->with(
            'success',
            'Hasil proyek berhasil dikirim. Admin akan memeriksa isi folder Google Drive dan memberikan nilai.',
        );
    }

    /**
     * @return array{
     *     number: int,
     *     name: string
     * }
     */
    private function projectArea(
        string $studyProgram,
        PortfolioProject $project,
    ): array {
        $program = AcademicProgramCatalog::program(
            $studyProgram,
        );

        if ($program === null) {
            return [
                'number' => PHP_INT_MAX,
                'name' => 'Bidang proyek',
            ];
        }

        $projectSkillSlugs = $project
            ->skills
            ->pluck(
                'slug',
            )
            ->sort()
            ->values()
            ->all();

        foreach (
            $program[
                'areas'
            ] as $index => $area
        ) {
            $areaSkillSlugs = collect(
                $area[
                    'skills'
                ],
            )
                ->pluck(
                    'slug',
                )
                ->sort()
                ->values()
                ->all();

            if (
                $projectSkillSlugs
                === $areaSkillSlugs
            ) {
                return [
                    'number' => $index + 1,
                    'name' => (string) $area[
                        'name'
                    ],
                ];
            }
        }

        return [
            'number' => PHP_INT_MAX,
            'name' => 'Bidang proyek',
        ];
    }
}
