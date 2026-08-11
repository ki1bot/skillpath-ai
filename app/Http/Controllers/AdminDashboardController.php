<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render(
            'admin/dashboard',
            [
                'stats' => [
                    'users' => User::query()
                        ->where('role', 'student')
                        ->count(),

                    'careers' => Career::query()
                        ->count(),

                    'skills' => Skill::query()
                        ->count(),

                    'materials' => LearningMaterial::query()
                        ->count(),

                    'projects' => PortfolioProject::query()
                        ->count(),

                    'assessmentAttempts' => DB::table(
                        'assessment_results',
                    )
                        ->distinct('attempt_uuid')
                        ->count('attempt_uuid'),
                ],

                'overview' => [
                    'activeCareers' => Career::query()
                        ->where('is_active', true)
                        ->count(),

                    'activeAssessments' => Assessment::query()
                        ->where('is_active', true)
                        ->count(),

                    'onboardedStudents' => User::query()
                        ->where('role', 'student')
                        ->whereNotNull(
                            'onboarding_completed_at',
                        )
                        ->count(),
                ],
            ],
        );
    }
}
