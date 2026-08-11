<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\LearningMaterial;
use App\Models\ProgressLog;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Models\UserSkill;
use App\Services\CareerReadinessService;
use App\Services\RoadmapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoadmapController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user()->load('targetCareer');

        $roadmap = Roadmap::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->with([
                'career',
                'items.material.skill.prerequisites',
            ])
            ->first();

        if (! $roadmap) {
            return redirect()
                ->route('assessment.show')
                ->with('error', 'Selesaikan tugas untuk membuat roadmap personal.');
        }

        return Inertia::render('roadmap', [
            'roadmap' => $roadmap,
        ]);
    }

    public function material(
        Request $request,
        LearningMaterial $material,
    ): Response {
        $item = RoadmapItem::query()
            ->where('learning_material_id', $material->id)
            ->whereHas(
                'roadmap',
                fn ($query) => $query
                    ->where('user_id', $request->user()->id)
                    ->where('is_active', true),
            )
            ->with([
                'material.skill.prerequisites',
                'evaluations' => fn ($query) => $query->latest(),
            ])
            ->firstOrFail();

        abort_if(
            $item->status === 'locked',
            403,
            'Materi ini masih terkunci.',
        );

        return Inertia::render('material', [
            'item' => $item,
            'material' => $item->material,
        ]);
    }

    public function logProgress(
        Request $request,
        RoadmapItem $roadmapItem,
    ): RedirectResponse {
        $this->authorizeItem($request, $roadmapItem);

        $validated = $request->validate([
            'progress_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:95',
            ],
            'minutes_spent' => [
                'required',
                'integer',
                'min:0',
                'max:1440',
            ],
            'notes' => ['nullable', 'string', 'max:2000'],
            'obstacle' => ['nullable', 'string', 'max:1000'],
            'evidence_url' => ['nullable', 'url', 'max:1000'],
        ]);

        $roadmapItem->update([
            'progress_percentage' => max(
                $roadmapItem->progress_percentage,
                $validated['progress_percentage'],
            ),
            'status' => $roadmapItem->status === 'needs_reinforcement'
                ? 'needs_reinforcement'
                : 'available',
        ]);

        ProgressLog::create([
            'user_id' => $request->user()->id,
            'roadmap_item_id' => $roadmapItem->id,
            'activity_type' => 'learning',
            'minutes_spent' => $validated['minutes_spent'],
            'progress_percentage' => $validated['progress_percentage'],
            'notes' => $validated['notes'] ?? null,
            'obstacle' => $validated['obstacle'] ?? null,
            'evidence_url' => $validated['evidence_url'] ?? null,
            'logged_at' => now(),
        ]);

        return back()
            ->with('success', 'Progres belajar tersimpan. Materi baru dianggap dikuasai setelah evaluasi lulus.');
    }

    public function evaluate(
        Request $request,
        RoadmapItem $roadmapItem,
        RoadmapService $roadmapService,
        CareerReadinessService $readinessService,
    ): RedirectResponse {
        $this->authorizeItem($request, $roadmapItem);
        $roadmapItem->load('material.skill');

        $validated = $request->validate([
            'answer' => ['required', 'string', 'max:10'],
        ]);

        $material = $roadmapItem->material;
        $passed = $validated['answer'] === $material->quiz_answer;
        $score = $passed ? 100 : 0;

        $feedback = $passed
            ? 'Jawaban tepat. Skill diperbarui dan langkah berikutnya akan dibuka jika seluruh prasyarat terpenuhi.'
            : ($material->quiz_explanation ?: 'Jawaban belum tepat. Pelajari kembali konsep inti lalu ulangi evaluasi.');

        Evaluation::create([
            'user_id' => $request->user()->id,
            'roadmap_item_id' => $roadmapItem->id,
            'score' => $score,
            'passed' => $passed,
            'answer' => $validated['answer'],
            'feedback' => $feedback,
        ]);

        if ($passed) {
            $user = $request->user()->load('targetCareer');

            $current = UserSkill::firstOrNew([
                'user_id' => $user->id,
                'skill_id' => $material->skill_id,
            ]);

            $targetSkill = $user->targetCareer?->skills()
                ->where('skills.id', $material->skill_id)
                ->first();

            $target = (float) ($targetSkill?->pivot->target_level ?? 80);
            $currentScore = (float) ($current->score ?? 0);

            $newScore = min(
                100,
                max($currentScore + 20, $target * 0.80),
            );

            $current->fill([
                'score' => round($newScore, 2),
                'source' => 'evaluation',
                'last_assessed_at' => now(),
            ])->save();

            $roadmapItem->update([
                'status' => 'completed',
                'progress_percentage' => 100,
                'completed_at' => now(),
                'evaluation_score' => $score,
            ]);
        } else {
            $roadmapItem->update([
                'status' => 'needs_reinforcement',
                'evaluation_score' => $score,
            ]);
        }

        ProgressLog::create([
            'user_id' => $request->user()->id,
            'roadmap_item_id' => $roadmapItem->id,
            'activity_type' => $passed
                ? 'evaluation_passed'
                : 'evaluation_failed',
            'minutes_spent' => 0,
            'progress_percentage' => $passed
                ? 100
                : $roadmapItem->progress_percentage,
            'notes' => $feedback,
            'logged_at' => now(),
        ]);

        $roadmapService->refreshAvailability($request->user());

        $readinessService->snapshot(
            $request->user(),
            $passed ? 'evaluation_passed' : 'evaluation_failed',
        );

        return back()->with(
            $passed ? 'success' : 'error',
            $feedback,
        );
    }

    private function authorizeItem(
        Request $request,
        RoadmapItem $item,
    ): void {
        $owned = $item->roadmap()
            ->where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->exists();

        abort_unless($owned, 403);
        abort_if(
            $item->status === 'locked',
            403,
            'Materi ini masih terkunci.',
        );
    }
}
