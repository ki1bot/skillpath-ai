<?php

namespace App\Http\Controllers;

use App\Models\Roadmap;
use App\Services\AdaptiveRoadmapService;
use App\Services\CareerReadinessService;
use App\Services\SkillGapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        CareerReadinessService $careerReadinessService,
        SkillGapService $skillGapService,
        AdaptiveRoadmapService $adaptiveRoadmapService,
    ): Response|RedirectResponse {
        $user = $request
            ->user()
            ->load('targetCareer');

        if (
            ! $user->onboarding_completed_at
            || ! $user->targetCareer
        ) {
            return redirect()
                ->route('onboarding.show');
        }

        $adaptiveRoadmapService
            ->adaptForInactivity($user);

        $roadmap = Roadmap::query()
            ->select([
                'id',
                'user_id',
                'version',
                'estimated_weeks',
            ])
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->with([
                'items:id,roadmap_id,learning_material_id,status,progress_percentage,position',
                'items.material:id,skill_id,title,slug',
                'items.material.skill:id,name',
            ])
            ->first();

        $analysis = $skillGapService
            ->analyze($user);

        $nextItem = $roadmap
            ?->items
            ->first(
                fn ($item) => in_array(
                    $item->status,
                    [
                        'available',
                        'needs_reinforcement',
                    ],
                    true,
                ),
            );

        $totalMinutes = (int) $user
            ->progressLogs()
            ->sum('minutes_spent');

        $today = now()
            ->startOfDay();

        $activityStart = $today
            ->copy()
            ->subDays(13);

        $activityByDate = $user
            ->progressLogs()
            ->where('logged_at', '>=', $activityStart)
            ->selectRaw(
                'DATE(logged_at) AS activity_date, SUM(minutes_spent) AS total_minutes',
            )
            ->groupByRaw('DATE(logged_at)')
            ->pluck('total_minutes', 'activity_date');

        $activity = collect(
            range(13, 0),
        )->map(
            function (
                int $daysAgo,
            ) use (
                $today,
                $activityByDate,
            ) {
                $date = $today
                    ->copy()
                    ->subDays($daysAgo);

                return [
                    'date' => $date
                        ->format('d M'),
                    'minutes' => (int) (
                        $activityByDate[
                            $date->toDateString()
                        ]
                        ?? 0
                    ),
                ];
            },
        );

        return Inertia::render(
            'dashboard',
            [
                'career' => $user
                    ->targetCareer,

                'readiness' => $careerReadinessService
                    ->calculate(
                        $user,
                        $analysis,
                        $roadmap,
                    ),

                'priorities' => collect(
                    $analysis,
                )
                    ->where('gap', '>', 0)
                    ->take(3)
                    ->values(),

                'skillChart' => collect(
                    $analysis,
                )
                    ->map(
                        fn (array $item) => [
                            'skill' => $item['name'],
                            'current' => $item['current'],
                            'target' => $item['target'],
                        ],
                    )
                    ->values(),

                'roadmap' => $roadmap
                    ? [
                        'version' => $roadmap
                            ->version,
                        'estimated_weeks' => $roadmap
                            ->estimated_weeks,
                        'total' => $roadmap
                            ->items
                            ->count(),
                        'completed' => $roadmap
                            ->items
                            ->where('status', 'completed')
                            ->count(),
                    ]
                    : null,

                'nextItem' => $nextItem
                    ? [
                        'id' => $nextItem->id,
                        'title' => $nextItem
                            ->material
                            ->title,
                        'slug' => $nextItem
                            ->material
                            ->slug,
                        'skill' => $nextItem
                            ->material
                            ->skill
                            ->name,
                        'status' => $nextItem
                            ->status,
                        'progress' => $nextItem
                            ->progress_percentage,
                    ]
                    : null,

                'totalStudyMinutes' => $totalMinutes,

                'activity' => $activity,

                'activeProject' => $user
                    ->projects()
                    ->whereIn(
                        'status',
                        [
                            'planned',
                            'in_progress',
                        ],
                    )
                    ->with('project')
                    ->latest('updated_at')
                    ->first(),
            ],
        );
    }
}
