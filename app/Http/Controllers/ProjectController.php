<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;
use App\Models\ProgressLog;
use App\Models\UserProject;
use App\Rules\ExternalEvidenceUrl;
use App\Services\AiInsightService;
use App\Services\CareerReadinessService;
use App\Services\ProjectReadinessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
                    'readiness' => $service->calculate($user, $project),
                    'user_project' => UserProject::query()
                        ->where('user_id', $user->id)
                        ->where('portfolio_project_id', $project->id)
                        ->first(),
                ];
            })
            ->sort(
                function (array $a, array $b): int {
                    $rankComparison = (
                        $a['readiness']['recommendation']['rank']
                        <=> $b['readiness']['recommendation']['rank']
                    );

                    if ($rankComparison !== 0) {
                        return $rankComparison;
                    }

                    $scoreComparison = (
                        $b['readiness']['score']
                        <=> $a['readiness']['score']
                    );

                    if ($scoreComparison !== 0) {
                        return $scoreComparison;
                    }

                    return (
                        $a['estimated_hours']
                        <=> $b['estimated_hours']
                    );
                },
            )
            ->values();

        return Inertia::render('projects', [
            'projects' => $projects,
        ]);
    }

    public function show(
        Request $request,
        PortfolioProject $portfolioProject,
        ProjectReadinessService $service,
        AiInsightService $aiInsightService,
    ): Response {
        $user = $request->user();

        abort_unless(
            $portfolioProject->career_id === $user->target_career_id,
            404,
        );

        $portfolioProject->load(['career', 'skills']);

        $userProject = UserProject::query()
            ->where('user_id', $user->id)
            ->where('portfolio_project_id', $portfolioProject->id)
            ->first();

        $readiness = $service->calculate(
            $user,
            $portfolioProject,
        );

        return Inertia::render('project-show', [
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
                        && is_string($aiFeedback['content'])
                        && trim($aiFeedback['content']) !== '';

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
        ]);
    }

    public function start(
        Request $request,
        PortfolioProject $portfolioProject,
        ProjectReadinessService $readinessService,
    ): RedirectResponse {
        $user = $request->user();

        abort_unless(
            $portfolioProject->career_id === $user->target_career_id,
            404,
        );

        $portfolioProject->loadMissing('skills');

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
                'started_at' => now(),
            ],
        );

        if (! $userProject->wasRecentlyCreated) {
            return back()->with(
                'success',
                'Proyek ini sudah pernah dimulai. Lanjutkan dari progres terakhir Anda.',
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
                ? 'Proyek dimulai sebagai challenge. Prioritaskan skill yang masih memiliki gap agar risiko pengerjaan tetap terkendali.'
                : 'Proyek dimulai. Gunakan checklist fitur sebagai batas minimum pengerjaan.',
        );
    }

    public function update(
        Request $request,
        PortfolioProject $portfolioProject,
        CareerReadinessService $readinessService,
    ): RedirectResponse {
        abort_unless(
            $portfolioProject->career_id
                === $request->user()->target_career_id,
            404,
        );

        $validated = $request->validate([
            'progress_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
            'repository_url' => [
                'nullable',
                'string',
                new ExternalEvidenceUrl,
                'max:1000',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ]);

        $completed = $validated['progress_percentage'] === 100;
        $repositoryUrl = trim(
            (string) (
                $validated['repository_url']
                ?? ''
            ),
        );
        $notes = trim(
            (string) (
                $validated['notes']
                ?? ''
            ),
        );

        if ($completed && $repositoryUrl === '') {
            throw ValidationException::withMessages([
                'repository_url' => 'Proyek hanya dapat ditandai 100% setelah tautan repository atau bukti eksternal disertakan.',
            ]);
        }

        if (
            $completed
            && Str::length($notes) < 80
        ) {
            throw ValidationException::withMessages([
                'notes' => 'Untuk menyelesaikan proyek, jelaskan hasil, bagian yang sudah berfungsi, dan bukti penyelesaian minimal 80 karakter.',
            ]);
        }

        $userProject = UserProject::query()
            ->where('user_id', $request->user()->id)
            ->where('portfolio_project_id', $portfolioProject->id)
            ->firstOrFail();

        $userProject->update([
            'progress_percentage' => $validated['progress_percentage'],
            'repository_url' => $repositoryUrl !== ''
                ? $repositoryUrl
                : null,
            'notes' => $notes !== ''
                ? $notes
                : null,
            'status' => $completed
                ? 'completed'
                : 'in_progress',
            'completed_at' => $completed
                ? now()
                : null,
        ]);

        ProgressLog::create([
            'user_id' => $request->user()->id,
            'activity_type' => $completed
                ? 'project_completed'
                : 'project_progress',
            'minutes_spent' => 0,
            'progress_percentage' => $validated['progress_percentage'],
            'notes' => $notes !== ''
                ? $notes
                : null,
            'evidence_url' => $repositoryUrl !== ''
                ? $repositoryUrl
                : null,
            'logged_at' => now(),
        ]);

        $readinessService->snapshot(
            $request->user(),
            $completed
                ? 'project_completed'
                : 'project_progress',
        );

        return back()->with(
            'success',
            $completed
                ? 'Proyek ditandai selesai dengan bukti eksternal yang tercatat.'
                : 'Progres proyek diperbarui.',
        );
    }
}
