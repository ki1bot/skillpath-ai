<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class AdminWorkspaceController extends Controller
{
    public function dashboard(Request $request): Response
    {
        if ($request->query('section') === 'manage') {
            return app(AdminController::class)->index();
        }

        return app(AdminDashboardController::class)();
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
        return redirect()->route('admin.dashboard', [
            'section' => 'manage',
        ]);
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
