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
    public function show(
        Request $request,
    ): Response|RedirectResponse {
        $user = $request->user();

        if (! $user->target_career_id) {
            return redirect()
                ->route('onboarding.show');
        }

        $assessment = Assessment::query()
            ->where(
                'career_id',
                $user->target_career_id,
            )
            ->where('is_active', true)
            ->with([
                'career',
                'questions.skill',
            ])
            ->firstOrFail();

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

        $assessment = Assessment::query()
            ->where(
                'career_id',
                $user->target_career_id,
            )
            ->where('is_active', true)
            ->with('questions')
            ->firstOrFail();

        $validated = $request->validate([
            'answers' => [
                'required',
                'array',
            ],
            'answers.*' => [
                'required',
                'string',
                'max:10',
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

            'responses' => [
                'nullable',
                'array',
            ],
            'responses.*' => [
                'nullable',
                'string',
                'max:4000',
            ],

            'evidence_urls' => [
                'nullable',
                'array',
            ],
            'evidence_urls.*' => [
                'nullable',
                'url',
                'max:1000',
            ],

            'experience_notes' => [
                'nullable',
                'array',
            ],
            'experience_notes.*' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'experience_evidence_urls' => [
                'nullable',
                'array',
            ],
            'experience_evidence_urls.*' => [
                'nullable',
                'url',
                'max:1000',
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

            if (
                $question->question_type
                === 'practical'
            ) {
                $response = trim(
                    (string) (
                        $validated['responses'][$question->id]
                        ?? ''
                    ),
                );

                if (
                    Str::length($response)
                    < 40
                ) {
                    throw ValidationException::withMessages([
                        "responses.{$question->id}" => 'Jelaskan hasil tugas praktik minimal 20 karakter.',
                    ]);
                }

                $evidenceUrl = $validated[
                    'evidence_urls'
                ][$question->id] ?? null;

                if (
                    $question->evidence_required
                    && ! $evidenceUrl
                ) {
                    throw ValidationException::withMessages([
                        "evidence_urls.{$question->id}" => 'Tautan bukti diperlukan untuk tugas praktik ini.',
                    ]);
                }
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

                    $responseText = trim(
                        (string) (
                            $validated[
                                'responses'
                            ][$question->id]
                            ?? ''
                        ),
                    );

                    $evidenceUrl = $validated[
                        'evidence_urls'
                    ][$question->id] ?? null;

                    $experienceNotes = trim(
                        (string) (
                            $validated[
                                'experience_notes'
                            ][$question->id]
                            ?? ''
                        ),
                    );

                    $experienceEvidenceUrl = $validated[
                        'experience_evidence_urls'
                    ][$question->id] ?? null;

                    $correct = (
                        $answer
                        === $question->correct_answer
                    );

                    $objectiveWeight = (
                        $question->question_type
                        === 'practical'
                    )
                        ? 60
                        : 80;

                    $score = $correct
                        ? $objectiveWeight
                        : 0;

                    $score += (
                        $selfRating
                        * 0.20
                    );

                    if (
                        $question->question_type
                        === 'practical'
                    ) {
                        if (
                            Str::length($responseText)
                            >= 40
                        ) {
                            $score += 10;
                        }

                        if ($evidenceUrl) {
                            $score += 10;
                        }
                    }

                    if (
                        Str::length(
                            $experienceNotes,
                        )
                        >= 40
                    ) {
                        $score += 2;
                    }

                    if ($experienceEvidenceUrl) {
                        $score += 3;
                    }

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

                        'response_text' => $responseText !== ''
                            ? $responseText
                            : null,

                        'evidence_url' => $evidenceUrl,

                        'experience_notes' => $experienceNotes !== ''
                            ? $experienceNotes
                            : null,

                        'experience_evidence_url' => $experienceEvidenceUrl,
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
            'Hasil Assesment '
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
                'Assesment selesai. Skill gap dan roadmap Anda sudah diperbarui.',
            );
    }
}
