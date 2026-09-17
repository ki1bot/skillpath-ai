<?php

namespace App\Services\Assessment;

use App\Models\Assessment;
use App\Models\User;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AssessmentContextService
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

    /**
     * @return array{
     *     user: User,
     *     study_program: string,
     *     assessment: Assessment,
     *     skill_slugs: list<string>
     * }|RedirectResponse
     */
    public function resolve(Request $request): array|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->target_career_id) {
            return redirect()
                ->route('onboarding.show');
        }

        $studyProgram = $this->resolveStudyProgram(
            $user->study_program,
        );

        if ($studyProgram === null) {
            return redirect()
                ->route('onboarding.show')
                ->withErrors([
                    'study_program' => 'Pilih salah satu jurusan yang tersedia sebelum melanjutkan ke Assessment.',
                ]);
        }

        $assessment = Assessment::query()
            ->where(
                'career_id',
                $user->target_career_id,
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

        if (! $assessment) {
            return redirect()
                ->route('onboarding.show')
                ->with(
                    'error',
                    'Assessment untuk jurusan ini belum tersedia. Periksa kembali data Assessment di server.',
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
                    'Konfigurasi skill Assessment jurusan belum lengkap.',
                );
        }

        return [
            'user' => $user,
            'study_program' => $studyProgram,
            'assessment' => $assessment,
            'skill_slugs' => $skillSlugs,
        ];
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
