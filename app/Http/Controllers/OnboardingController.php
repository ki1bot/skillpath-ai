<?php

namespace App\Http\Controllers;

use App\Models\Career;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    public function show(Request $request): Response
    {
        return Inertia::render('onboarding', [
            'careers' => Career::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'profile' => $request->user()->only([
                'study_program',
                'semester',
                'interest_area',
                'experience',
                'weekly_study_hours',
                'target_career_id',
                'onboarding_completed_at',
            ]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'study_program' => ['required', 'string', 'max:120'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'interest_area' => ['required', 'string', 'max:120'],
            'experience' => ['required', 'string', 'max:1000'],
            'weekly_study_hours' => ['required', 'integer', 'min:1', 'max:60'],
            'target_career_id' => ['required', 'integer', 'exists:careers,id'],
        ]);

        $user = $request->user();
        $wasOnboarded = $user->onboarding_completed_at !== null;
        $targetChanged = (int) $user->target_career_id !== (int) $validated['target_career_id'];

        $user->update([
            ...$validated,
            'onboarding_completed_at' => now(),
        ]);

        if ($targetChanged) {
            $user->roadmaps()
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        if (! $wasOnboarded || $targetChanged) {
            return redirect()
                ->route('assessment.show')
                ->with('success', 'Profil tersimpan. Sekarang ukur kemampuan awal Anda untuk target karier ini.');
        }

        $roadmap = $user->roadmaps()
            ->where('is_active', true)
            ->with('items.material')
            ->first();

        if ($roadmap) {
            $remainingMinutes = $roadmap->items
                ->where('status', '!=', 'completed')
                ->sum(fn ($item) => (int) $item->material->estimated_minutes);

            $weeklyMinutes = max(((int) $user->weekly_study_hours) * 60, 60);

            $roadmap->update([
                'estimated_weeks' => max(
                    (int) ceil($remainingMinutes / $weeklyMinutes),
                    1,
                ),
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profil belajar diperbarui. Estimasi roadmap sudah menyesuaikan waktu belajar terbaru.');
    }
}
