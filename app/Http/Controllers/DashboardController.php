<?php

namespace App\Http\Controllers;

use App\Models\Roadmap;
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
    ): Response|RedirectResponse {
        $user = $request->user()->load('targetCareer');

        if ($user->isAdmin()) {
            return redirect()->route('admin.index');
        }

        if (! $user->onboarding_completed_at || ! $user->targetCareer) {
            return redirect()->route('onboarding.show');
        }

        $roadmap = Roadmap::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->with(['items.material.skill'])
            ->first();

        $analysis = $skillGapService->analyze($user);

        $nextItem = $roadmap?->items
            ->first(fn ($item) => in_array(
                $item->status,
                ['available', 'needs_reinforcement'],
                true,
            ));

        $totalMinutes = (int) $user->progressLogs()
            ->sum('minutes_spent');

        $activity = collect(range(13, 0))
            ->map(function (int $daysAgo) use ($user) {
                $date = now()->subDays($daysAgo)->startOfDay();

                $minutes = (int) $user->progressLogs()
                    ->whereBetween(
                        'logged_at',
                        [$date, $date->copy()->endOfDay()],
                    )
                    ->sum('minutes_spent');

                return [
                    'date' => $date->format('d M'),
                    'minutes' => $minutes,
                ];
            });

        return Inertia::render('dashboard', [
            'career' => $user->targetCareer,
            'readiness' => $careerReadinessService->calculate($user),
            'priorities' => collect($analysis)
                ->where('gap', '>', 0)
                ->take(3)
                ->values(),
            'skillChart' => collect($analysis)
                ->map(fn (array $item) => [
                    'skill' => $item['name'],
                    'current' => $item['current'],
                    'target' => $item['target'],
                ])
                ->values(),
            'roadmap' => $roadmap ? [
                'version' => $roadmap->version,
                'estimated_weeks' => $roadmap->estimated_weeks,
                'total' => $roadmap->items->count(),
                'completed' => $roadmap->items
                    ->where('status', 'completed')
                    ->count(),
            ] : null,
            'nextItem' => $nextItem ? [
                'id' => $nextItem->id,
                'title' => $nextItem->material->title,
                'slug' => $nextItem->material->slug,
                'skill' => $nextItem->material->skill->name,
                'status' => $nextItem->status,
                'progress' => $nextItem->progress_percentage,
            ] : null,
            'totalStudyMinutes' => $totalMinutes,
            'activity' => $activity,
            'activeProject' => $user->projects()
                ->whereIn('status', ['planned', 'in_progress'])
                ->with('project')
                ->latest('updated_at')
                ->first(),
        ]);
    }
}
