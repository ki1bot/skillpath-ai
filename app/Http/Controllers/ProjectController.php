<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;
use App\Models\ProgressLog;
use App\Models\UserProject;
use App\Rules\GoogleDriveSubmissionFolder;
use App\Services\AiInsightService;
use App\Services\ProjectReadinessService;
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
        $user = $request->user();

        if (! $user->target_career_id) {
            return redirect()->route('onboarding.show');
        }

        $projects = PortfolioProject::query()
            ->where('career_id', $user->target_career_id)
            ->with('skills')
            ->get()
            ->map(function (PortfolioProject $project) use ($user, $service) {
                return [
                    ...$project->toArray(),
                    'readiness' => $service->calculate(
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
            })
            ->sort(
                function (array $a, array $b): int {
                    $rankComparison = $a['readiness']['recommendation']['rank']
                        <=> $b['readiness']['recommendation']['rank'];

                    if ($rankComparison !== 0) {
                        return $rankComparison;
                    }

                    $scoreComparison = $b['readiness']['score']
                        <=> $a['readiness']['score'];

                    if ($scoreComparison !== 0) {
                        return $scoreComparison;
                    }

                    return $a['estimated_hours']
                        <=> $b['estimated_hours'];
                },
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

                        $generated = $aiFeedback['generated_by_ai']
                            && is_string(
                                $aiFeedback['content'],
                            )
                            && trim(
                                $aiFeedback['content'],
                            ) !== '';

                        return [
                            'content' => $aiFeedback['content'],
                            'generatedByAi' => $generated,
                            'model' => $aiFeedback['model'],
                            'message' => $generated
                                ? null
                                : 'Umpan balik AI sedang tidak tersedia. Silakan coba lagi.',
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
                .$readiness['recommendation']['label'].'.',
            'logged_at' => now(),
        ]);

        return back()->with(
            'success',
            $readiness['recommendation']['level'] === 'challenge'
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

        if ($current->status === 'completed') {
            throw ValidationException::withMessages([
                'repository_url' => 'Proyek ini sudah lulus dan tidak perlu dikumpulkan ulang.',
            ]);
        }

        if ($current->review_status === 'pending') {
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
            (string) $validated['repository_url'],
        );

        DB::transaction(
            function () use (
                $user,
                $portfolioProject,
                $current,
                $googleDriveUrl,
            ): void {
                $userProject = UserProject::query()
                    ->whereKey(
                        $current->id,
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($userProject->status === 'completed') {
                    throw ValidationException::withMessages([
                        'repository_url' => 'Proyek ini sudah lulus dan tidak perlu dikumpulkan ulang.',
                    ]);
                }

                if ($userProject->review_status === 'pending') {
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
}
