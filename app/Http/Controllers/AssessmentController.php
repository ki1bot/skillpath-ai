<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use App\Models\UserSkill;
use App\Services\CareerReadinessService;
use App\Services\RoadmapService;
use App\Support\AcademicAssessmentCatalog;
use App\Support\SkillPathScoringPolicy;
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

        $questionPool = $this->questionPool(
            $assessment,
            $skillSlugs,
        );

        if (
            ! $this->hasValidQuestionPool(
                $questionPool,
                $studyProgram,
                $skillSlugs,
            )
        ) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Bank soal Assesment belum lengkap. Setiap jurusan harus memiliki 30 soal dari 9 skill inti sebelum Assesment dapat dimulai.',
                );
        }

        $sessionKey = $this->questionSessionKey(
            $assessment->id,
            $user->id,
        );

        $reserveSessionKey = $this->reserveQuestionSessionKey(
            $assessment->id,
            $user->id,
        );

        $questionIds = $this->normalizeQuestionIds(
            $request
                ->session()
                ->get($sessionKey),
        );

        $reserveQuestionIds = $this->normalizeQuestionIds(
            $request
                ->session()
                ->get($reserveSessionKey),
        );

        $hadStoredSession = (
            $questionIds !== []
            || $reserveQuestionIds !== []
        );

        $started = $this->hasValidQuestionSession(
            $questionPool,
            $questionIds,
            $reserveQuestionIds,
        );

        if (
            $hadStoredSession
            && ! $started
        ) {
            $this->forgetQuestionSession(
                $request,
                $assessment->id,
                $user->id,
            );

            return redirect()
                ->route('assessment.show')
                ->with(
                    'error',
                    'Sesi Assesment sebelumnya sudah tidak berlaku. Silakan mulai Assesment kembali.',
                );
        }

        $questions = $started
            ? $this->questionsInStoredOrder(
                $questionPool,
                $questionIds,
            )
            : $questionPool->take(0);

        $payload = [
            'id' => $assessment->id,
            'study_program' => $assessment->study_program,
            'title' => $assessment->title,
            'description' => $assessment->description,
            'duration_minutes' => $assessment->duration_minutes,
            'question_limit' => AcademicAssessmentCatalog::QUESTION_LIMIT,
            'reserve_question_count' => AcademicAssessmentCatalog::RESERVE_QUESTION_LIMIT,
            'skill_count' => AcademicAssessmentCatalog::SKILLS_PER_PROGRAM,
            'started' => $started,
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

    public function start(
        Request $request,
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

        $questionPool = $this->questionPool(
            $assessment,
            $skillSlugs,
        );

        if (
            ! $this->hasValidQuestionPool(
                $questionPool,
                $studyProgram,
                $skillSlugs,
            )
        ) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Bank soal Assesment belum lengkap. Setiap jurusan harus memiliki 30 soal dari 9 skill inti sebelum Assesment dapat dimulai.',
                );
        }

        $questionSession = $this->buildQuestionSession(
            $questionPool,
            $skillSlugs,
        );

        if (
            ! $this->hasValidQuestionSession(
                $questionPool,
                $questionSession['question_ids'],
                $questionSession['reserve_question_ids'],
            )
        ) {
            return redirect()
                ->route('assessment.show')
                ->with(
                    'error',
                    'Sistem gagal menyiapkan soal Assesment secara lengkap. Silakan coba kembali.',
                );
        }

        $request
            ->session()
            ->put(
                $this->questionSessionKey(
                    $assessment->id,
                    $user->id,
                ),
                $questionSession[
                    'question_ids'
                ],
            );

        $request
            ->session()
            ->put(
                $this->reserveQuestionSessionKey(
                    $assessment->id,
                    $user->id,
                ),
                $questionSession[
                    'reserve_question_ids'
                ],
            );

        return redirect()
            ->route('assessment.show');
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

        $questionPool = $this->questionPool(
            $assessment,
            $skillSlugs,
        );

        if (
            ! $this->hasValidQuestionPool(
                $questionPool,
                $studyProgram,
                $skillSlugs,
            )
        ) {
            $this->forgetQuestionSession(
                $request,
                $assessment->id,
                $user->id,
            );

            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Bank soal Assesment sudah berubah dan sesi tidak dapat dilanjutkan.',
                );
        }

        $questionIds = $this->normalizeQuestionIds(
            $request
                ->session()
                ->get(
                    $this->questionSessionKey(
                        $assessment->id,
                        $user->id,
                    ),
                ),
        );

        $reserveQuestionIds = $this->normalizeQuestionIds(
            $request
                ->session()
                ->get(
                    $this->reserveQuestionSessionKey(
                        $assessment->id,
                        $user->id,
                    ),
                ),
        );

        if (
            ! $this->hasValidQuestionSession(
                $questionPool,
                $questionIds,
                $reserveQuestionIds,
            )
        ) {
            $this->forgetQuestionSession(
                $request,
                $assessment->id,
                $user->id,
            );

            return redirect()
                ->route('assessment.show')
                ->with(
                    'error',
                    'Sesi Assesment belum dimulai atau sudah tidak berlaku. Silakan mulai Assesment kembali.',
                );
        }

        $questions = $this->questionsInStoredOrder(
            $questionPool,
            $questionIds,
        );

        if (
            $questions->count()
            !== AcademicAssessmentCatalog::QUESTION_LIMIT
        ) {
            $this->forgetQuestionSession(
                $request,
                $assessment->id,
                $user->id,
            );

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

        $expectedIds = $questionIds;
        sort($expectedIds);

        $answerIds = array_map(
            'intval',
            array_keys(
                $validated[
                    'answers'
                ],
            ),
        );

        sort($answerIds);

        if ($answerIds !== $expectedIds) {
            throw ValidationException::withMessages([
                'answers' => 'Jawab tepat '
                    .AcademicAssessmentCatalog::QUESTION_LIMIT
                    .' pertanyaan yang diberikan pada sesi Assesment ini.',
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
                        ? SkillPathScoringPolicy::ASSESSMENT_CORRECT_SCORE
                        : SkillPathScoringPolicy::ASSESSMENT_INCORRECT_SCORE;

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

        $this->forgetQuestionSession(
            $request,
            $assessment->id,
            $user->id,
        );

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
     * @param  list<string>  $skillSlugs
     * @return Collection<int, AssessmentQuestion>
     */
    private function questionPool(
        Assessment $assessment,
        array $skillSlugs,
    ): Collection {
        return $assessment
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
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questions
     * @param  list<string>  $skillSlugs
     */
    private function hasValidQuestionPool(
        Collection $questions,
        string $studyProgram,
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

            $expectedCount = AcademicAssessmentCatalog::questionCapacityForSkill(
                $studyProgram,
                $skillSlug,
            );

            if ($questionCount !== $expectedCount) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questionPool
     * @param  list<string>  $skillSlugs
     * @return array{
     *     question_ids: list<int>,
     *     reserve_question_ids: list<int>
     * }
     */
    private function buildQuestionSession(
        Collection $questionPool,
        array $skillSlugs,
    ): array {
        $reducedSkillSlugs = collect(
            $skillSlugs,
        )
            ->shuffle()
            ->take(
                AcademicAssessmentCatalog::REDUCED_SKILLS_PER_SESSION,
            )
            ->values()
            ->all();

        $questionIds = [];

        foreach ($skillSlugs as $skillSlug) {
            $questionLimit = in_array(
                $skillSlug,
                $reducedSkillSlugs,
                true,
            )
                ? AcademicAssessmentCatalog::BASE_QUESTIONS_PER_SKILL - 1
                : AcademicAssessmentCatalog::BASE_QUESTIONS_PER_SKILL;

            $skillQuestions = $questionPool
                ->filter(
                    fn (AssessmentQuestion $question) => $question
                        ->skill
                        ?->slug === $skillSlug,
                )
                ->shuffle()
                ->take(
                    $questionLimit,
                );

            foreach ($skillQuestions as $question) {
                $questionIds[] = (int) $question->id;
            }
        }

        $questionIds = collect(
            $questionIds,
        )
            ->shuffle()
            ->map(
                fn ($id) => (int) $id,
            )
            ->values()
            ->all();

        $questionLookup = array_fill_keys(
            $questionIds,
            true,
        );

        $reserveQuestionIds = $questionPool
            ->filter(
                fn (AssessmentQuestion $question) => ! isset(
                    $questionLookup[
                        $question->id
                    ],
                ),
            )
            ->shuffle()
            ->pluck('id')
            ->map(
                fn ($id) => (int) $id,
            )
            ->take(
                AcademicAssessmentCatalog::RESERVE_QUESTION_LIMIT,
            )
            ->values()
            ->all();

        return [
            'question_ids' => $questionIds,
            'reserve_question_ids' => $reserveQuestionIds,
        ];
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questionPool
     * @param  list<int>  $questionIds
     * @param  list<int>  $reserveQuestionIds
     */
    private function hasValidQuestionSession(
        Collection $questionPool,
        array $questionIds,
        array $reserveQuestionIds,
    ): bool {
        if (
            count($questionIds)
            !== AcademicAssessmentCatalog::QUESTION_LIMIT
        ) {
            return false;
        }

        if (
            count($reserveQuestionIds)
            !== AcademicAssessmentCatalog::RESERVE_QUESTION_LIMIT
        ) {
            return false;
        }

        if (
            array_intersect(
                $questionIds,
                $reserveQuestionIds,
            ) !== []
        ) {
            return false;
        }

        $storedIds = array_merge(
            $questionIds,
            $reserveQuestionIds,
        );

        if (
            count(
                array_unique(
                    $storedIds,
                ),
            )
            !== AcademicAssessmentCatalog::QUESTION_POOL_SIZE
        ) {
            return false;
        }

        $poolIds = $questionPool
            ->pluck('id')
            ->map(
                fn ($id) => (int) $id,
            )
            ->values()
            ->all();

        sort($storedIds);
        sort($poolIds);

        if ($storedIds !== $poolIds) {
            return false;
        }

        $questionLookup = array_fill_keys(
            $questionIds,
            true,
        );

        $skillCounts = [];

        foreach ($questionPool as $question) {
            if (
                ! isset(
                    $questionLookup[
                        $question->id
                    ],
                )
            ) {
                continue;
            }

            $skillId = (int) $question
                ->skill_id;

            $skillCounts[
                $skillId
            ] = (
                $skillCounts[
                    $skillId
                ] ?? 0
            ) + 1;
        }

        sort($skillCounts);

        return $skillCounts === [
            2,
            2,
            3,
            3,
            3,
            3,
            3,
            3,
            3,
        ];
    }

    /**
     * @return list<int>
     */
    private function normalizeQuestionIds(
        mixed $value,
    ): array {
        if (! is_array($value)) {
            return [];
        }

        $questionIds = [];

        foreach ($value as $id) {
            $questionId = (int) $id;

            if ($questionId <= 0) {
                continue;
            }

            $questionIds[] = $questionId;
        }

        return array_values(
            array_unique(
                $questionIds,
            ),
        );
    }

    /**
     * @param  Collection<int, AssessmentQuestion>  $questionPool
     * @param  list<int>  $questionIds
     * @return Collection<int, AssessmentQuestion>
     */
    private function questionsInStoredOrder(
        Collection $questionPool,
        array $questionIds,
    ): Collection {
        $positions = array_flip(
            $questionIds,
        );

        return $questionPool
            ->filter(
                fn (AssessmentQuestion $question) => array_key_exists(
                    $question->id,
                    $positions,
                ),
            )
            ->sortBy(
                fn (AssessmentQuestion $question) => $positions[
                    $question->id
                ],
            )
            ->values();
    }

    private function forgetQuestionSession(
        Request $request,
        int $assessmentId,
        int $userId,
    ): void {
        $request
            ->session()
            ->forget([
                $this->questionSessionKey(
                    $assessmentId,
                    $userId,
                ),
                $this->reserveQuestionSessionKey(
                    $assessmentId,
                    $userId,
                ),
            ]);
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

    private function reserveQuestionSessionKey(
        int $assessmentId,
        int $userId,
    ): string {
        return 'assessment.reserve_question_ids.'
            .$assessmentId
            .'.'
            .$userId;
    }
}
