<?php

namespace App\Http\Controllers;

use App\Services\Assessment\AssessmentAbandonService;
use App\Services\Assessment\AssessmentShowService;
use App\Services\Assessment\AssessmentStartService;
use App\Services\Assessment\AssessmentSubmitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class AssessmentController extends Controller
{
    public function show(
        Request $request,
        AssessmentShowService $service,
    ): Response|RedirectResponse {
        return $service->handle(
            $request,
        );
    }

    public function start(
        Request $request,
        AssessmentStartService $service,
    ): RedirectResponse {
        return $service->handle(
            $request,
        );
    }

    public function abandon(
        Request $request,
        AssessmentAbandonService $service,
    ): RedirectResponse {
        return $service->handle(
            $request,
        );
    }

    public function submit(
        Request $request,
        AssessmentSubmitService $service,
    ): RedirectResponse {
        return $service->handle(
            $request,
        );
    }
}
