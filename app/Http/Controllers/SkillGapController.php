<?php

namespace App\Http\Controllers;

use App\Services\AiExplanationService;
use App\Services\SkillGapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SkillGapController extends Controller
{
    public function index(
        Request $request,
        SkillGapService $skillGapService,
        AiExplanationService $aiExplanationService,
    ): Response|RedirectResponse|JsonResponse {
        $user = $request
            ->user()
            ->load('targetCareer');

        if (! $user->targetCareer) {
            if ($request->boolean('ai')) {
                return response()->json(
                    [
                        'summary' => null,
                    ],
                    422,
                );
            }

            return redirect()->route(
                'onboarding.show',
            );
        }

        $analysis = $skillGapService
            ->analyze($user);

        if ($request->boolean('ai')) {
            $summary = $aiExplanationService
                ->skillGapSummary(
                    $user,
                    $analysis,
                );

            if ($summary === null) {
                return response()
                    ->json(
                        [
                            'summary' => null,
                        ],
                        503,
                    )
                    ->header(
                        'Cache-Control',
                        'no-store',
                    );
            }

            return response()
                ->json([
                    'summary' => $summary,
                ])
                ->header(
                    'Cache-Control',
                    'no-store',
                );
        }

        return Inertia::render(
            'skills',
            [
                'career' => $user
                    ->targetCareer,

                'skills' => $analysis,

                'averageMastery' => $skillGapService
                    ->averageMastery(
                        $user,
                        $analysis,
                    ),
            ],
        );
    }
}
