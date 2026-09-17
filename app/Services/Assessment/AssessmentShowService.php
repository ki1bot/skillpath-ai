<?php

namespace App\Services\Assessment;

use App\Models\AssessmentResult;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssessmentShowService
{
    public function __construct(
        private readonly AssessmentContextService $contextService,
        private readonly AssessmentQuestionPoolService $poolService,
        private readonly AssessmentSessionService $sessionService,
    ) {}

    public function handle(
        Request $request,
    ): Response|RedirectResponse {
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

        $assessment->load('career');

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
                    'Bank soal Assessment belum lengkap. Setiap jurusan harus memiliki tepat '
                        .AcademicAssessmentCatalog::QUESTION_LIMIT
                        .' soal sebelum Assessment dapat dimulai.',
                );
        }

        $state = $this->sessionService->state(
            $request,
            $assessment,
            $user->id,
            $questionPool,
        );

        if (
            $state['has_stored_session']
            && ! $state['started']
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
                    'Sesi Assessment sebelumnya sudah tidak berlaku. Silakan mulai Assessment kembali.',
                );
        }

        $questions = $state['started']
            ? $this->sessionService->questionsInStoredOrder(
                $questionPool,
                $state['question_ids'],
            )
            : $questionPool->take(0);

        return Inertia::render(
            'assessment',
            [
                'assessment' => [
                    'id' => $assessment->id,
                    'user_id' => $user->id,
                    'study_program' => $assessment->study_program,
                    'title' => $assessment->title,
                    'description' => $assessment->description,
                    'duration_minutes' => $assessment->duration_minutes,
                    'question_limit' => AcademicAssessmentCatalog::QUESTION_LIMIT,
                    'skill_count' => AcademicAssessmentCatalog::SKILLS_PER_PROGRAM,
                    'started' => $state['started'],
                    'career' => [
                        'name' => $assessment
                            ->career
                            ->name,
                    ],
                    'questions' => $this->poolService->toPayload(
                        $questions,
                    ),
                ],
                'latestAttempt' => AssessmentResult::query()
                    ->where(
                        'user_id',
                        $user->id,
                    )
                    ->where(
                        'assessment_id',
                        $assessment->id,
                    )
                    ->latest()
                    ->value('attempt_uuid'),
            ],
        );
    }
}
