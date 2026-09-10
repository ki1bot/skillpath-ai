<?php

namespace App\Services\Assessment;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssessmentStartService
{
    public function __construct(
        private readonly AssessmentContextService $contextService,
        private readonly AssessmentQuestionPoolService $poolService,
        private readonly AssessmentSessionService $sessionService,
    ) {}

    public function handle(
        Request $request,
    ): RedirectResponse {
        $context = $this->contextService->resolve(
            $request,
        );

        if ($context instanceof RedirectResponse) {
            return $context;
        }

        $assessment = $context['assessment'];
        $user = $context['user'];
        $studyProgram = $context['study_program'];
        $skillSlugs = $context['skill_slugs'];

        $questionPool = $this->poolService->load(
            $assessment,
            $skillSlugs,
        );

        if (
            ! $this->poolService->isValid(
                $questionPool,
                $studyProgram,
                $skillSlugs,
            )
        ) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Bank soal Assesment belum lengkap. Setiap jurusan harus memiliki tepat 30 soal sebelum Assesment dapat dimulai.',
                );
        }

        if (
            ! $this->sessionService->start(
                $request,
                $assessment,
                $user->id,
                $questionPool,
            )
        ) {
            return redirect()
                ->route('assessment.show')
                ->with(
                    'error',
                    'Sistem gagal menyiapkan 30 soal Assesment. Silakan coba kembali.',
                );
        }

        return redirect()
            ->route('assessment.show');
    }
}
