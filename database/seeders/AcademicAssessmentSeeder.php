<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Career;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Database\Seeder;

class AcademicAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $programs = array_keys(
            AcademicAssessmentCatalog::programs(),
        );

        Assessment::query()
            ->whereNull('study_program')
            ->update([
                'is_active' => false,
            ]);

        Assessment::query()
            ->whereNotNull('study_program')
            ->whereNotIn(
                'study_program',
                $programs,
            )
            ->update([
                'is_active' => false,
            ]);

        $careers = Career::query()
            ->where(
                'is_active',
                true,
            )
            ->whereIn(
                'name',
                $programs,
            )
            ->get()
            ->keyBy('name');

        foreach ($programs as $studyProgram) {
            $career = $careers->get(
                $studyProgram,
            );

            if (! $career) {
                continue;
            }

            Assessment::updateOrCreate(
                [
                    'career_id' => $career->id,
                    'study_program' => $studyProgram,
                ],
                [
                    'title' => 'Assesment Awal '.$studyProgram,
                    'description' => 'Kerjakan 30 pertanyaan yang diacak untuk setiap sesi Assesment jurusan '
                        .$studyProgram
                        .'. Seluruh pertanyaan berasal dari bank soal Assesment jurusan dan tidak dibuat berdasarkan materi pembelajaran.',
                    'duration_minutes' => 30,
                    'is_active' => true,
                ],
            );
        }
    }
}
