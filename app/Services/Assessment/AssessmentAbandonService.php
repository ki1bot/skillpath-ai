<?php

namespace App\Services\Assessment;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssessmentAbandonService
{
    public function __construct(
        private readonly AssessmentContextService $contextService,
        private readonly AssessmentSessionService $sessionService,
    ) {}

    public function handle(Request $request): RedirectResponse
    {
        $context = $this->contextService->resolve(
            $request,
        );

        if ($context instanceof RedirectResponse) {
            return $context;
        }

        $this->sessionService->clear(
            $request,
            $context['assessment'],
            $context['user']->id,
        );

        return redirect()
            ->route('assessment.show')
            ->with(
                'success',
                'Sesi Assesment dibatalkan. Progres Assesment sebelumnya sudah dihapus.',
            );
    }
}
