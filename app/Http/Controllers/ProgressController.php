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

        $readiness = null;

        $getReadiness = function () use (
            &$readiness,
            $readinessService,
            $user,
        ): array {
            if ($readiness === null) {
                $readiness = $readinessService
                    ->calculate($user);
            }

            return $readiness;
        };

        return Inertia::render(
            'progress',
            [
                'readiness' => fn () => $getReadiness(),

                'aiInsights' => Inertia::defer(
                    function () use (
                        $aiInsightService,
                        $getReadiness,
                        $user,
                    ): array {
                        $aiInsights = $aiInsightService
                            ->progress(
                                $user,
                                $getReadiness(),
                            );

                        return [
                            'progress' => $aiInsights[
                                'progress'
                            ],
                            'schedule' => $aiInsights[
                                'schedule'
                            ],
                            'obstacles' => $aiInsights[
                                'obstacles'
                            ],
                            'generatedByAi' => $aiInsights[
                                'generated_by_ai'
                            ],
                            'model' => $aiInsights[
                                'model'
                            ],
                            'message' => $aiInsights[
                                'generated_by_ai'
                            ]
                                ? null
                                : 'AI Learning Coach sedang tidak tersedia. Silakan coba lagi.',
                        ];
                    },
                ),

                'readinessHistory' => fn () => $user
                    ->readinessSnapshots()
                    ->with(
                        'career:id,name,slug',
                    )
                    ->oldest()
                    ->get(),

                'assessmentHistory' => fn () => $user
                    ->assessmentResults()
                    ->with(
                        'skill:id,name',
                    )
                    ->latest()
                    ->limit(120)
                    ->get()
                    ->groupBy(
                        'attempt_uuid',
                    )
                    ->map(
                        function ($rows) {
                            return [
                                'attempt_uuid' => $rows
                                    ->first()
                                    ->attempt_uuid,

                                'date' => $rows
                                    ->first()
                                    ->created_at
                                    ?->format(
                                        'd M Y H:i',
                                    ),

                                'average' => round(
                                    (float) $rows
                                        ->avg('score'),
                                    1,
                                ),

                                'skills' => $rows
                                    ->map(
                                        fn ($row) => [
                                            'name' => $row
                                                ->skill
                                                ?->name,
                                            'score' => $row
                                                ->score,
                                        ],
                                    )
                                    ->values(),
                            ];
                        },
                    )
                    ->values(),

                'logs' => fn () => $user
                    ->progressLogs()
                    ->with(
                        'roadmapItem.material:id,title,slug',
                    )
                    ->latest(
                        'logged_at',
                    )
                    ->limit(30)
                    ->get(),

                'evaluations' => fn () => $user
                    ->evaluations()
                    ->with(
                        'roadmapItem.material:id,title,slug',
                    )
                    ->latest()
                    ->limit(20)
                    ->get(),

                'projects' => fn () => $user
                    ->projects()
                    ->with(
                        'project:id,title,slug',
                    )
                    ->latest(
                        'updated_at',
                    )
                    ->get(),

                'roadmaps' => fn () => $user
                    ->roadmaps()
                    ->with(
                        'career:id,name,slug',
                    )
                    ->latest()
                    ->limit(10)
                    ->get(),
            ],
        );
    }
}
