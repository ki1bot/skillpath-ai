<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminWorkspaceController extends Controller
{
    public function dashboard(Request $request): Response
    {
        return Inertia::render('admin/index', [
            'stats' => Inertia::always([
                'users' => User::query()
                    ->where('role', 'student')
                    ->count(),

                'careers' => Career::query()
                    ->count(),

                'skills' => Skill::query()
                    ->count(),

                'materials' => LearningMaterial::query()
                    ->where('is_active', true)
                    ->count(),

                'projects' => PortfolioProject::query()
                    ->count(),

                'assessmentAttempts' => DB::table(
                    'assessment_results',
                )
                    ->distinct('attempt_uuid')
                    ->count('attempt_uuid'),
            ]),

            'careers' => Inertia::defer(
                fn () => Career::query()
                    ->with('skills')
                    ->orderBy('name')
                    ->get(),
                'management',
            ),

            'skills' => Inertia::defer(
                fn () => Skill::query()
                    ->with('prerequisites')
                    ->orderBy('name')
                    ->get(),
                'management',
            ),

            'prerequisites' => Inertia::defer(
                fn () => DB::table('skill_prerequisites')
                    ->join(
                        'skills as skill',
                        'skill.id',
                        '=',
                        'skill_prerequisites.skill_id',
                    )
                    ->join(
                        'skills as prerequisite',
                        'prerequisite.id',
                        '=',
                        'skill_prerequisites.prerequisite_skill_id',
                    )
                    ->select([
                        'skill_prerequisites.id',
                        'skill_prerequisites.factor',
                        'skill.name as skill_name',
                        'prerequisite.name as prerequisite_name',
                    ])
                    ->orderBy('skill.name')
                    ->get(),
                'management',
            ),

            'assessments' => Inertia::defer(
                fn () => Assessment::query()
                    ->with([
                        'career',
                        'questions.skill',
                    ])
                    ->orderBy('title')
                    ->get(),
                'management',
            ),

            'materials' => Inertia::defer(
                fn () => LearningMaterial::query()
                    ->with('skill')
                    ->orderBy('title')
                    ->get(),
                'management',
            ),

            'projects' => Inertia::defer(
                fn () => PortfolioProject::query()
                    ->with([
                        'career',
                        'skills',
                    ])
                    ->orderBy('title')
                    ->get(),
                'management',
            ),
        ]);
    }

    public function submissions(Request $request): Response
    {
        if ($request->query('type') === 'project') {
            return app(ProjectSubmissionController::class)
                ->index($request);
        }

        return app(EvaluationSubmissionController::class)
            ->index($request);
    }

    public function legacyDashboard(): RedirectResponse
    {
        return redirect()->route('admin.dashboard');
    }

    public function legacyProjectSubmissions(
        Request $request,
    ): RedirectResponse {
        return redirect()->route('admin.submissions.index', [
            'type' => 'project',
            ...$request->only(['status', 'page']),
        ]);
    }
}
