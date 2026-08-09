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
        $user = $request->user()->load('targetCareer');

        if (! $user->targetCareer) {
            return redirect()->route('onboarding.show');
        }

        $analysis = $skillGapService->analyze($user);

        return Inertia::render('skills', [
            'career' => $user->targetCareer,
            'skills' => $analysis,
            'summary' => $aiExplanationService->skillGapSummary($user, $analysis),
            'averageMastery' => $skillGapService->averageMastery($user),
        ]);
    }
}
