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
                return response()
                    ->json(
                        [
                            'summary' => null,
                            'generated_by_ai' => false,
                            'model' => null,
                        ],
                        422,
                    )
                    ->header(
                        'Cache-Control',
                        'no-store',
                    );
            }

            return redirect()->route(
                'onboarding.show',
            );
        }

        $analysis = $skillGapService
            ->analyze($user);

        if ($request->boolean('ai')) {
            $result = $aiExplanationService
                ->skillGapSummary(
                    $user,
                    $analysis,
                );

            return response()
                ->json([
                    'summary' => $result->summary,
                    'generated_by_ai' => $result->generatedByAi,
                    'model' => $result->model,
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
