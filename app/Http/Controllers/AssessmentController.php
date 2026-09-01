<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\UserSkill;
use App\Services\CareerReadinessService;
use App\Services\RoadmapService;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AssessmentController extends Controller
{
    private const STUDY_PROGRAM_ALIASES = [
        'sistem informasi' => 'Sistem Informasi',
        'si' => 'Sistem Informasi',
        'manajemen' => 'Manajemen',
        'teknik informatika' => 'Teknik Informatika',
        'informatika' => 'Teknik Informatika',
        'ti' => 'Teknik Informatika',
        'sistem komputer' => 'Sistem Komputer',
        'sk' => 'Sistem Komputer',
        'psikologi' => 'Psikologi',
        'ilmu komunikasi' => 'Ilmu Komunikasi',
        'ikom' => 'Ilmu Komunikasi',
    ];

    public function show(
        Request $request,
    ): Response|RedirectResponse {
        $user = $request->user();

        if (! $user->target_career_id) {
            return redirect()
                ->route('onboarding.show');
        }

        $studyProgram = $this->resolveStudyProgram(
            $user->study_program,
        );

        if (! $studyProgram) {
            return redirect()
                ->route('onboarding.show')
                ->withErrors([
                    'study_program' => 'Pilih salah satu jurusan yang tersedia sebelum melanjutkan ke Assesment.',
                ]);
        }

        $assessment = $this->findAssessment(
            $user->target_career_id,
            $studyProgram,
        );

        if (! $assessment) {
            return redirect()
                ->route('onboarding.show')
                ->with(
                    'error',
                    'Assesment untuk jurusan ini belum tersedia. Periksa kembali data Assesment di server.',
                );
        }

        $skillSlugs = AcademicAssessmentCatalog::skillSlugs(
            $studyProgram,
        );

        if (
            count($skillSlugs)
            !== AcademicAssessmentCatalog::SKILLS_PER_PROGRAM
        ) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Konfigurasi skill Assesment jurusan belum lengkap.',
                );
        }

        $assessment->load('career');

        $questionPool = $assessment
            ->questions()
            ->whereHas(
                'skill',
                fn ($query) => $query->whereIn(
                    'slug',
                    $skillSlugs,
                ),
            )
            ->with('skill')
            ->get();

        if (
            ! $this->hasValidQuestionPool(
                $questionPool,
                $skillSlugs,
            )
        ) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Bank soal Assesment belum lengkap. Setiap jurusan harus memiliki 27 soal dari 9 skill inti, dengan 3 soal pada setiap skill.',
                );
        }

        $questions = $questionPool
            ->shuffle()
            ->take(
                AcademicAssessmentCatalog::QUESTION_LIMIT,
            )
            ->values();

        $request->session()->put(
            $this->questionSessionKey(
                $assessment->id,
                $user->id,
            ),
            $questions
                ->pluck('id')
                ->map(
                    fn ($id) => (int) $id,
                )
                ->values()
                ->all(),
        );

        $payload = [
            'id' => $assessment->id,
            'study_program' => $assessment->study_program,
            'title' => $assessment->title,
            'description' => $assessment->description,
            'duration_minutes' => $assessment->duration_minutes,
            'career' => [
                'name' => $assessment
                    ->career
                    ->name,
            ],
            'questions' => $questions
                ->map(
                    fn (AssessmentQuestion $question) => [
                        'id' => $question->id,
                        'question_type' => $question
                            ->question_type,
                        'prompt' => $question
                            ->prompt,
                        'practical_instructions' => $question
                            ->practical_instructions,
                        'evidence_required' => $question
                            ->evidence_required,
                        'options' => $question
                            ->options,
                        'difficulty' => $question
                            ->difficulty,
                        'skill' => [
                            'id' => $question
                                ->skill
                                ->id,
                            'name' => $question
                                ->skill
                                ->name,
                            'category' => $question
                                ->skill
                                ->category,
                        ],
                    ],
                )
                ->values(),
        ];

        return Inertia::render(
            'assessment',
            [
                'assessment' => $payload,
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

    public function submit(
        Request $request,
        RoadmapService $roadmapService,
        CareerReadinessService $readinessService,
    ): RedirectResponse {
        $user = $request->user();

        if (! $user->target_career_id) {
            return redirect()
                ->route('onboarding.show');
        }

        $studyProgram = $this->resolveStudyProgram(
            $user->study_program,
        );

        if (! $studyProgram) {
            return redirect()
                ->route('onboarding.show')
                ->withErrors([
                    'study_program' => 'Pilih salah satu jurusan yang tersedia sebelum melanjutkan ke Assesment.',
                ]);
        }

        $assessment = $this->findAssessment(
            $user->target_career_id,
            $studyProgram,
        );

        if (! $assessment) {
            return redirect()
                ->route('onboarding.show')
                ->with(
                    'error',
                    'Assesment untuk jurusan ini belum tersedia. Periksa kembali data Assesment di server.',
                );
        }

        $skillSlugs = AcademicAssessmentCatalog::skillSlugs(
            $studyProgram,
        );

        $sessionKey = $this->questionSessionKey(
            $assessment->id,
            $user->id,
        );

        $storedQuestionIds = $request
            ->session()
            ->get($sessionKey);

        if (! is_array($storedQuestionIds)) {
            return redirect()
                ->route('assessment.show')
                ->with(
                    'error',
                    'Sesi Assesment sudah tidak berlaku. Soal baru sudah disiapkan, silakan kerjakan kembali.',
                );
        }

        $questionIds = collect(
            $storedQuestionIds,
        )
            ->map(
                fn ($id) => (int) $id,
            )
            ->filter(
                fn (int $id) => $id > 0,
            )
            ->unique()
            ->values();

        if (
            $questionIds->count()
            !== AcademicAssessmentCatalog::QUESTION_LIMIT
        ) {
            $request
                ->session()
                ->forget($sessionKey);

            return redirect()
                ->route('assessment.show')
                ->with(
                    'error',
                    'Sesi Assesment tidak valid. Soal baru sudah disiapkan.',
                );
        }

        $questions = $assessment
            ->questions()
            ->whereIn(
                'id',
                $questionIds->all(),
            )
            ->whereHas(
                'skill',
                fn ($query) => $query->whereIn(
                    'slug',
                    $skillSlugs,
                ),
            )
            ->get();

        if (
            $questions->count()
            !== AcademicAssessmentCatalog::QUESTION_LIMIT
        ) {
            $request
                ->session()
                ->forget($sessionKey);

            return redirect()
                ->route('assessment.show')
                ->with(
                    'error',
                    'Sebagian soal Assesment sudah berubah. Silakan mulai Assesment kembali.',
                );
        }

        $validated = $request->validate([
            'answers' => [
                'required',
                'array',
            ],
            'answers.*' => [
                'required',
                'string',
                'in:A,B,C,D',
            ],
        ]);

        $expectedIds = $questionIds
            ->sort()
            ->values()
            ->all();

        $answerIds = collect(
            array_keys(
                $validated['answers'],
            ),
        )
            ->map(
                fn ($id) => (int) $id,
            )
            ->sort()
            ->values()
            ->all();

        if ($answerIds !== $expectedIds) {
            throw ValidationException::withMessages([
                'answers' => 'Jawab tepat 25 pertanyaan yang diberikan pada sesi Assesment ini.',
            ]);
        }

        foreach ($questions as $question) {
            if (
                ! array_key_exists(
                    $question->id,
                    $validated['answers'],
                )
            ) {
                throw ValidationException::withMessages([
                    'answers' => 'Jawab semua pertanyaan sebelum menyelesaikan Assesment.',
                ]);
            }
        }

        $attemptUuid = (string) Str::uuid();

        DB::transaction(
            function () use (
                $assessment,
                $questions,
                $validated,
                $user,
                $attemptUuid,
            ) {
                $skillScores = [];

                foreach ($questions as $question) {
                    $answer = (string) $validated[
                        'answers'
                    ][$question->id];

                    $correct = (
                        $answer
                        === $question->correct_answer
                    );

                    $score = $correct
                        ? 100.0
                        : 0.0;

                    AssessmentResult::create([
                        'user_id' => $user->id,
                        'assessment_id' => $assessment
                            ->id,
                        'assessment_question_id' => $question
                            ->id,
                        'skill_id' => $question
                            ->skill_id,
                        'attempt_uuid' => $attemptUuid,
                        'score' => $score,
                        'is_correct' => $correct,
                        'answer' => $answer,
                        'response_text' => null,
                        'evidence_url' => null,
                        'experience_notes' => null,
                        'experience_evidence_url' => null,
                    ]);

                    $skillScores[
                        $question->skill_id
                    ][] = $score;
                }

                foreach (
                    $skillScores as $skillId => $scores
                ) {
                    $average = round(
                        array_sum($scores)
                            / count($scores),
                        2,
                    );

                    UserSkill::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'skill_id' => $skillId,
                        ],
                        [
                            'score' => $average,
                            'source' => 'assessment',
                            'last_assessed_at' => now(),
                        ],
                    );
                }
            },
        );

        $request
            ->session()
            ->forget($sessionKey);

        $freshUser = $user->fresh([
            'targetCareer',
        ]);

        $roadmapService->regenerate(
            $freshUser,
            'Hasil Assesment '.$studyProgram.' '
                .now()->format('d M Y'),
        );

        $readinessService->snapshot(
            $freshUser,
            'assessment_completed',
        );

        return redirect()
            ->route('skills.index')
            ->with(
                'success',
                'Assesment '.$studyProgram.' selesai. Hasil kemampuanmu sudah disimpan dan roadmap diperbarui.',
            );
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questions
     * @param  list<string>  $skillSlugs
     */
    private function hasValidQuestionPool(
        Collection $questions,
        array $skillSlugs,
    ): bool {
        if (
            $questions->count()
            !== AcademicAssessmentCatalog::QUESTION_POOL_SIZE
        ) {
            return false;
        }

        if (
            $questions
                ->pluck('skill.slug')
                ->filter()
                ->unique()
                ->count()
            !== AcademicAssessmentCatalog::SKILLS_PER_PROGRAM
        ) {
            return false;
        }

        foreach ($skillSlugs as $skillSlug) {
            $questionCount = $questions
                ->filter(
                    fn (AssessmentQuestion $question) => $question
                        ->skill
                        ?->slug === $skillSlug,
                )
                ->count();

            if (
                $questionCount
                !== AcademicAssessmentCatalog::QUESTIONS_PER_SKILL
            ) {
                return false;
            }
        }

        return true;
    }

    private function findAssessment(
        int $careerId,
        string $studyProgram,
    ): ?Assessment {
        return Assessment::query()
            ->where(
                'career_id',
                $careerId,
            )
            ->where(
                'study_program',
                $studyProgram,
            )
            ->where(
                'is_active',
                true,
            )
            ->first();
    }

    private function resolveStudyProgram(
        ?string $studyProgram,
    ): ?string {
        if (! $studyProgram) {
            return null;
        }

        $normalized = Str::lower(
            Str::squish($studyProgram),
        );

        return self::STUDY_PROGRAM_ALIASES[
            $normalized
        ] ?? null;
    }

    private function questionSessionKey(
        int $assessmentId,
        int $userId,
    ): string {
        return 'assessment.question_ids.'
            .$assessmentId
            .'.'
            .$userId;
    }
}
