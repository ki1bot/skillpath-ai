<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Models\User;
use App\Services\AcademicStatisticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminWorkspaceController extends Controller
{
    public function dashboard(
        Request $request,
        AcademicStatisticsService $statistics,
    ): Response {
        $stats = [
            'users' => User::query()
                ->where('role', 'student')
                ->count(),

            'careers' => $statistics->careers,

            'skills' => Skill::query()
                ->count(),

            'materials' => LearningMaterial::query()
                ->where('is_active', true)
                ->count(),

            'learningTopics' => $statistics->topics,

            'assessmentQuestions' => $statistics->questions,

            'projects' => $statistics->projects,

            'assessmentAttempts' => DB::table(
                'assessment_results',
            )
                ->distinct('attempt_uuid')
                ->count('attempt_uuid'),
        ];

        $careers = Career::query()
            ->with('skills')
            ->orderBy('name')
            ->get();

        $skills = Skill::query()
            ->with('prerequisites')
            ->orderBy('name')
            ->get();

        $prerequisites = DB::table('skill_prerequisites')
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
            ->get();

        $assessments = Assessment::query()
            ->with([
                'career',
                'questions.skill',
            ])
            ->orderBy('title')
            ->get();

        $materials = LearningMaterial::query()
            ->with('skill')
            ->orderBy('title')
            ->get();

        $projects = PortfolioProject::query()
            ->with([
                'career',
                'skills',
            ])
            ->orderBy('title')
            ->get();

        return Inertia::render('admin/index', [
            'stats' => $stats,
            'careers' => $careers,
            'skills' => $skills,
            'prerequisites' => $prerequisites,
            'assessments' => $assessments,
            'materials' => $materials,
            'projects' => $projects,
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
