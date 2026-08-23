<?php

namespace Database\Seeders;

use App\Models\Assessment;
use Illuminate\Database\Seeder;

class AcademicAssessmentCleanupSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            'Sistem Informasi' => 'si-',
            'Manajemen' => 'man-',
            'Teknik Informatika' => 'ti-',
            'Psikologi' => 'psi-',
            'Ilmu Komunikasi' => 'ikom-',
        ];

        foreach ($programs as $studyProgram => $skillPrefix) {
            $assessments = Assessment::query()
                ->where(
                    'study_program',
                    $studyProgram,
                )
                ->get();

            foreach ($assessments as $assessment) {
                $assessment
                    ->questions()
                    ->whereHas(
                        'skill',
                        fn ($query) => $query->where(
                            'slug',
                            'not like',
                            $skillPrefix.'%',
                        ),
                    )
                    ->delete();

                $assessment->update([
                    'duration_minutes' => 18,
                ]);
            }
        }
    }
}
