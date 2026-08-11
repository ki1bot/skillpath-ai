<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\UserSkill;
use App\Services\CareerReadinessService;
use App\Services\RoadmapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AssessmentController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user->target_career_id) {
            return redirect()->route('onboarding.show');
        }

        $assessment = Assessment::query()
            ->where('career_id', $user->target_career_id)
            ->where('is_active', true)
            ->with(['career', 'questions.skill'])
            ->firstOrFail();

        return Inertia::render('assessment', [
            'assessment' => $assessment,
            'latestAttempt' => AssessmentResult::query()
                ->where('user_id', $user->id)
                ->where('assessment_id', $assessment->id)
                ->latest()
                ->value('attempt_uuid'),
        ]);
    }

    public function submit(
        Request $request,
        RoadmapService $roadmapService,
        CareerReadinessService $readinessService,
    ): RedirectResponse {
        $user = $request->user();

        $assessment = Assessment::query()
            ->where('career_id', $user->target_career_id)
            ->where('is_active', true)
            ->with('questions')
            ->firstOrFail();

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'string', 'max:10'],
            'self_ratings' => ['required', 'array'],
            'self_ratings.*' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        foreach ($assessment->questions as $question) {
            if (
                ! array_key_exists($question->id, $validated['answers'])
                || ! array_key_exists($question->id, $validated['self_ratings'])
            ) {
                throw ValidationException::withMessages([
                    'answers' => 'Semua pertanyaan dan self-rating harus diisi sebelum penilaian dikirim.',
                ]);
            }
        }

        $attemptUuid = (string) Str::uuid();

        foreach ($assessment->questions as $question) {
            $answer = (string) $validated['answers'][$question->id];
            $selfRating = (int) $validated['self_ratings'][$question->id];
            $correct = $answer === $question->correct_answer;
            $score = round(($correct ? 70 : 0) + ($selfRating * 0.30), 2);

            AssessmentResult::create([
                'user_id' => $user->id,
                'assessment_id' => $assessment->id,
                'skill_id' => $question->skill_id,
                'attempt_uuid' => $attemptUuid,
                'score' => $score,
                'is_correct' => $correct,
                'self_rating' => $selfRating,
                'answer' => $answer,
            ]);

            UserSkill::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'skill_id' => $question->skill_id,
                ],
                [
                    'score' => $score,
                    'source' => 'assessment',
                    'last_assessed_at' => now(),
                ],
            );
        }

        $freshUser = $user->fresh(['targetCareer']);

        $roadmapService->regenerate(
            $freshUser,
            'Hasil Penilaian '.now()->format('d M Y'),
        );

        $readinessService->snapshot(
            $freshUser,
            'assessment_completed',
        );

        return redirect()
            ->route('skills.index')
            ->with('success', 'Penilaian selesai. Skill gap dan roadmap Anda sudah diperbarui.');
    }
}
