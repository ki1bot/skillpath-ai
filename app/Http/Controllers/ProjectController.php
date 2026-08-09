<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;
use App\Models\ProgressLog;
use App\Models\UserProject;
use App\Services\CareerReadinessService;
use App\Services\ProjectReadinessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->orderBy('estimated_hours')
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
            });

        return Inertia::render('projects', [
            'projects' => $projects,
        ]);
    }

    public function show(
        Request $request,
        PortfolioProject $portfolioProject,
        ProjectReadinessService $service,
    ): Response {
        abort_unless(
            $portfolioProject->career_id === $request->user()->target_career_id,
            404,
        );

        $portfolioProject->load(['career', 'skills']);

        return Inertia::render('project-show', [
            'project' => $portfolioProject,
            'readiness' => $service->calculate(
                $request->user(),
                $portfolioProject,
            ),
            'userProject' => UserProject::query()
                ->where('user_id', $request->user()->id)
                ->where('portfolio_project_id', $portfolioProject->id)
                ->first(),
        ]);
    }

    public function start(
        Request $request,
        PortfolioProject $portfolioProject,
    ): RedirectResponse {
        abort_unless(
            $portfolioProject->career_id === $request->user()->target_career_id,
            404,
        );

        UserProject::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'portfolio_project_id' => $portfolioProject->id,
            ],
            [
                'status' => 'in_progress',
                'started_at' => now(),
            ],
        );

        return back()
            ->with('success', 'Proyek dimulai. Gunakan checklist fitur sebagai batas minimum pengerjaan.');
    }

    public function update(
        Request $request,
        PortfolioProject $portfolioProject,
        CareerReadinessService $readinessService,
    ): RedirectResponse {
        $validated = $request->validate([
            'progress_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
            'repository_url' => ['nullable', 'url', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $userProject = UserProject::query()
            ->where('user_id', $request->user()->id)
            ->where('portfolio_project_id', $portfolioProject->id)
            ->firstOrFail();

        $completed = $validated['progress_percentage'] === 100;

        $userProject->update([
            ...$validated,
            'status' => $completed ? 'completed' : 'in_progress',
            'completed_at' => $completed ? now() : null,
        ]);

        ProgressLog::create([
            'user_id' => $request->user()->id,
            'activity_type' => $completed
                ? 'project_completed'
                : 'project_progress',
            'minutes_spent' => 0,
            'progress_percentage' => $validated['progress_percentage'],
            'notes' => $validated['notes'] ?? null,
            'evidence_url' => $validated['repository_url'] ?? null,
            'logged_at' => now(),
        ]);

        $readinessService->snapshot(
            $request->user(),
            $completed ? 'project_completed' : 'project_progress',
        );

        return back()->with(
            'success',
            $completed
                ? 'Proyek ditandai selesai.'
                : 'Progres proyek diperbarui.',
        );
    }
}
