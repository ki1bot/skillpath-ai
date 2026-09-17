<?php

namespace App\Services\Assessment;

use App\Models\User;
use App\Services\CareerReadinessService;
use App\Services\RoadmapService;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssessmentSubmitService
{
    public function __construct(
        private readonly AssessmentContextService $contextService,
        private readonly AssessmentQuestionPoolService $poolService,
        private readonly AssessmentSessionService $sessionService,
        private readonly AssessmentResultService $resultService,
        private readonly RoadmapService $roadmapService,
        private readonly CareerReadinessService $readinessService,
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
            $this->sessionService->clear(
                $request,
                $assessment,
                $user->id,
            );

            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Bank soal AssesAssessmentment sudah berubah dan sesi tidak dapat dilanjutkan.',
                );
        }

        $state = $this->sessionService->state(
            $request,
            $assessment,
            $user->id,
            $questionPool,
        );

        if (! $state['started']) {
            $this->sessionService->clear(
                $request,
                $assessment,
                $user->id,
            );

            return redirect()
                ->route('assessment.show')
                ->with(
                    'error',
                    'Sesi Assessment belum dimulai atau sudah tidak berlaku. Silakan mulai Assessment kembali.',
                );
        }

        $questions = $this->sessionService->questionsInStoredOrder(
            $questionPool,
            $state['question_ids'],
        );

        if (
            $questions->count()
            !== AcademicAssessmentCatalog::QUESTION_LIMIT
        ) {
            $this->sessionService->clear(
                $request,
                $assessment,
                $user->id,
            );

            return redirect()
                ->route('assessment.show')
                ->with(
                    'error',
                    'Sebagian soal Assessment sudah berubah. Silakan mulai Assessment kembali.',
                );
        }

        $answers = $this->resultService->validatedAnswers(
            $request,
            $questions,
            $state['question_ids'],
        );

        $this->resultService->store(
            $user,
            $assessment,
            $questions,
            $answers,
        );

        $this->sessionService->clear(
            $request,
            $assessment,
            $user->id,
        );

        /** @var User $freshUser */
        $freshUser = $user->fresh([
            'targetCareer',
        ]) ?? $user;

        $this->roadmapService->regenerate(
            $freshUser,
            'Hasil Assessment '.$studyProgram.' '
                .now()->format('d M Y'),
        );

        $this->readinessService->snapshot(
            $freshUser,
            'assessment_completed',
        );

        return redirect()
            ->route('skills.index')
            ->with(
                'success',
                'Assessment '.$studyProgram.' selesai. Hasil kemampuanmu sudah disimpan dan roadmap diperbarui.',
            );
    }
}
