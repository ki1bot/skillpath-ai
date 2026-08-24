<?php

namespace App\Http\Controllers;

use App\Models\Career;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    private const STUDY_PROGRAMS = [
        'Sistem Informasi',
        'Manajemen',
        'Teknik Informatika',
        'Sistem Komputer',
        'Psikologi',
        'Ilmu Komunikasi',
    ];

    private const STUDY_PROGRAM_SLUGS = [
        'sistem-informasi',
        'manajemen',
        'teknik-informatika',
        'sistem-komputer',
        'psikologi',
        'ilmu-komunikasi',
    ];

    public function show(
        Request $request,
    ): Response {
        return Inertia::render(
            'onboarding',
            [
                'careers' => Career::query()
                    ->where(
                        'is_active',
                        true,
                    )
                    ->whereIn(
                        'slug',
                        self::STUDY_PROGRAM_SLUGS,
                    )
                    ->orderByRaw(
                        "
                        CASE slug
                            WHEN 'sistem-informasi' THEN 1
                            WHEN 'manajemen' THEN 2
                            WHEN 'teknik-informatika' THEN 3
                            WHEN 'sistem-komputer' THEN 4
                            WHEN 'psikologi' THEN 5
                            WHEN 'ilmu-komunikasi' THEN 6
                            ELSE 7
                        END
                        ",
                    )
                    ->get(),

                'profile' => $request
                    ->user()
                    ->only([
                        'study_program',
                        'semester',
                        'interest_area',
                        'experience',
                        'weekly_study_hours',
                        'target_career_id',
                        'onboarding_completed_at',
                    ]),
            ],
        );
    }

    public function update(
        Request $request,
    ): RedirectResponse {
        $validated = $request->validate([
            'semester' => [
                'required',
                'integer',
                'min:1',
                'max:14',
            ],
            'interest_area' => [
                'required',
                'string',
                'max:120',
            ],
            'experience' => [
                'required',
                'string',
                'max:1000',
            ],
            'weekly_study_hours' => [
                'required',
                'integer',
                'min:1',
                'max:60',
            ],
            'target_career_id' => [
                'required',
                'integer',
                Rule::exists(
                    'careers',
                    'id',
                )->where(
                    fn ($query) => $query
                        ->where(
                            'is_active',
                            true,
                        )
                        ->whereIn(
                            'slug',
                            self::STUDY_PROGRAM_SLUGS,
                        ),
                ),
            ],
        ]);

        $career = Career::query()
            ->whereKey(
                $validated['target_career_id'],
            )
            ->where(
                'is_active',
                true,
            )
            ->whereIn(
                'slug',
                self::STUDY_PROGRAM_SLUGS,
            )
            ->firstOrFail();

        if (
            ! in_array(
                $career->name,
                self::STUDY_PROGRAMS,
                true,
            )
        ) {
            throw ValidationException::withMessages([
                'target_career_id' => 'Jurusan yang dipilih tidak tersedia.',
            ]);
        }

        $user = $request->user();

        $wasOnboarded = (
            $user->onboarding_completed_at
            !== null
        );

        $targetChanged = (
            (int) $user->target_career_id
            !== (int) $career->id
        );

        $studyProgramChanged = (
            (string) $user->study_program
            !== $career->name
        );

        $assessmentContextChanged = (
            $targetChanged
            || $studyProgramChanged
        );

        $user->update([
            'study_program' => $career->name,
            'semester' => $validated[
                'semester'
            ],
            'interest_area' => $validated[
                'interest_area'
            ],
            'experience' => $validated[
                'experience'
            ],
            'weekly_study_hours' => $validated[
                'weekly_study_hours'
            ],
            'target_career_id' => $career->id,
            'onboarding_completed_at' => now(),
        ]);

        if ($assessmentContextChanged) {
            $user
                ->roadmaps()
                ->where(
                    'is_active',
                    true,
                )
                ->update([
                    'is_active' => false,
                ]);
        }

        if (
            ! $wasOnboarded
            || $assessmentContextChanged
        ) {
            return redirect()
                ->route(
                    'assessment.show',
                )
                ->with(
                    'success',
                    'Profil belajarmu sudah tersimpan. Sekarang lanjutkan dengan assesment untuk jurusan '.$career->name.'.',
                );
        }

        $roadmap = $user
            ->roadmaps()
            ->where(
                'is_active',
                true,
            )
            ->with(
                'items.material',
            )
            ->first();

        if ($roadmap) {
            $remainingMinutes = $roadmap
                ->items
                ->where(
                    'status',
                    '!=',
                    'completed',
                )
                ->sum(
                    fn ($item) => (
                        (int) $item
                            ->material
                            ->estimated_minutes
                    ),
                );

            $weeklyMinutes = max(
                (
                    (int) $user
                        ->weekly_study_hours
                ) * 60,
                60,
            );

            $roadmap->update([
                'estimated_weeks' => max(
                    (int) ceil(
                        $remainingMinutes
                        / $weeklyMinutes,
                    ),
                    1,
                ),
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Profil belajarmu sudah diperbarui.',
            );
    }
}
