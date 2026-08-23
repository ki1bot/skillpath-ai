<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\UserSkill;
use App\Services\CareerReadinessService;
use App\Services\RoadmapService;
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
                    'study_program' => 'Program studi harus salah satu dari: Sistem Informasi, Manajemen, Teknik Informatika, Psikologi, atau Ilmu Komunikasi.',
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
                    'Assesment untuk program studi ini belum tersedia. Jalankan migration dan seeder assesment akademik terlebih dahulu.',
                );
        }

        $assessment->load([
            'career',
            'questions.skill',
        ]);

        $payload = [
            'id' => $assessment->id,
            'title' => $assessment->title,
            'description' => $assessment->description,
            'duration_minutes' => $assessment->duration_minutes,
            'career' => [
                'name' => $assessment
                    ->career
                    ->name,
            ],
            'questions' => $assessment
                ->questions
                ->sortBy('id')
                ->map(
                    fn ($question) => [
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
                'profileExperience' => $user
                    ->experience,
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
                    'study_program' => 'Program studi harus salah satu dari: Sistem Informasi, Manajemen, Teknik Informatika, Psikologi, atau Ilmu Komunikasi.',
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
                    'Assesment untuk program studi ini belum tersedia. Jalankan migration dan seeder assesment akademik terlebih dahulu.',
                );
        }

        $assessment->load('questions');

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
            'self_ratings' => [
                'required',
                'array',
            ],
            'self_ratings.*' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
        ]);

        foreach ($assessment->questions as $question) {
            if (
                ! array_key_exists(
                    $question->id,
                    $validated['answers'],
                )
                || ! array_key_exists(
                    $question->id,
                    $validated['self_ratings'],
                )
            ) {
                throw ValidationException::withMessages([
                    'answers' => 'Semua pertanyaan dan penilaian diri harus diisi sebelum assesment dikirim.',
                ]);
            }
        }

        $attemptUuid = (string) Str::uuid();

        DB::transaction(
            function () use (
                $assessment,
                $validated,
                $user,
                $attemptUuid,
            ) {
                $skillScores = [];

                foreach (
                    $assessment->questions as $question
                ) {
                    $answer = (string) $validated[
                        'answers'
                    ][$question->id];

                    $selfRating = (int) $validated[
                        'self_ratings'
                    ][$question->id];

                    $correct = (
                        $answer
                        === $question->correct_answer
                    );

                    $score = (
                        $correct
                            ? 80
                            : 0
                    ) + (
                        $selfRating
                        * 0.20
                    );

                    $score = round(
                        min(
                            $score,
                            100,
                        ),
                        2,
                    );

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
                        'self_rating' => $selfRating,
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
                'Assesment '.$studyProgram.' selesai. Profil skill dan roadmap Anda sudah diperbarui.',
            );
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
            ->where('is_active', true)
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
}
