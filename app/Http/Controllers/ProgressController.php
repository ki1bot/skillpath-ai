<?php

namespace App\Http\Controllers;

use App\Services\AiInsightService;
use App\Services\CareerReadinessService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProgressController extends Controller
{
    public function index(
        Request $request,
        CareerReadinessService $readinessService,
        AiInsightService $aiInsightService,
    ): Response {
        $user = $request->user();

        $attempts = $user->assessmentResults()
            ->with('skill:id,name')
            ->latest()
            ->limit(120)
            ->get()
            ->groupBy('attempt_uuid')
            ->map(function ($rows) {
                return [
                    'attempt_uuid' => $rows->first()->attempt_uuid,
                    'date' => $rows->first()->created_at?->format('d M Y H:i'),
                    'average' => round((float) $rows->avg('score'), 1),
                    'skills' => $rows->map(fn ($row) => [
                        'name' => $row->skill?->name,
                        'score' => $row->score,
                    ])->values(),
                ];
            })
            ->values();

        $readiness = $readinessService->calculate(
            $user,
        );

        $aiInsights = $aiInsightService
            ->progress(
                $user,
                $readiness,
            );

        return Inertia::render('progress', [
            'readiness' => $readiness,
            'aiInsights' => [
                'progress' => $aiInsights['progress'],
                'schedule' => $aiInsights['schedule'],
                'obstacles' => $aiInsights['obstacles'],
                'generatedByAi' => $aiInsights['generated_by_ai'],
                'model' => $aiInsights['model'],
            ],
            'readinessHistory' => $user->readinessSnapshots()
                ->with('career:id,name,slug')
                ->oldest()
                ->get(),
            'assessmentHistory' => $attempts,
            'logs' => $user->progressLogs()
                ->with('roadmapItem.material:id,title,slug')
                ->latest('logged_at')
                ->limit(30)
                ->get(),
            'evaluations' => $user->evaluations()
                ->with('roadmapItem.material:id,title,slug')
                ->latest()
                ->limit(20)
                ->get(),
            'projects' => $user->projects()
                ->with('project:id,title,slug')
                ->latest('updated_at')
                ->get(),
            'roadmaps' => $user->roadmaps()
                ->with('career:id,name,slug')
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}
