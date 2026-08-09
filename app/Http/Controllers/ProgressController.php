<?php

namespace App\Http\Controllers;

use App\Services\CareerReadinessService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProgressController extends Controller
{
    public function index(
        Request $request,
        CareerReadinessService $readinessService,
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

        return Inertia::render('progress', [
            'readiness' => $readinessService->calculate($user),
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
