<?php

namespace App\Http\Controllers;

use App\Services\AiExplanationService;
use App\Services\SkillGapService;
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
    ): Response|RedirectResponse {
        $user = $request
            ->user()
            ->load('targetCareer');

        if (! $user->targetCareer) {
            return redirect()->route(
                'onboarding.show',
            );
        }

        $analysis = null;

        $getAnalysis = function () use (
            &$analysis,
            $skillGapService,
            $user,
        ): array {
            if ($analysis === null) {
                $analysis = $skillGapService
                    ->analyze($user);
            }

            return $analysis;
        };

        return Inertia::render(
            'skills',
            [
                'career' => $user
                    ->targetCareer,

                'skills' => fn () => $getAnalysis(),

                'summary' => Inertia::defer(
                    fn () => $aiExplanationService
                        ->skillGapSummary(
                            $user,
                            $getAnalysis(),
                        ),
                ),

                'averageMastery' => fn () => $skillGapService
                    ->averageMastery(
                        $user,
                        $getAnalysis(),
                    ),
            ],
        );
    }
}
