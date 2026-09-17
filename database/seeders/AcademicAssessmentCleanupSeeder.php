<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Support\AcademicAssessmentCatalog;
use Illuminate\Database\Seeder;

class AcademicAssessmentCleanupSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            'sistem-informasi' => [
                'name' => 'Sistem Informasi',
                'prefix' => 'si-',
            ],
            'manajemen' => [
                'name' => 'Manajemen',
                'prefix' => 'man-',
            ],
            'teknik-informatika' => [
                'name' => 'Teknik Informatika',
                'prefix' => 'ti-',
            ],
            'sistem-komputer' => [
                'name' => 'Sistem Komputer',
                'prefix' => 'sk-',
            ],
            'psikologi' => [
                'name' => 'Psikologi',
                'prefix' => 'psi-',
            ],
            'ilmu-komunikasi' => [
                'name' => 'Ilmu Komunikasi',
                'prefix' => 'ikom-',
            ],
        ];

        Assessment::query()
            ->whereNull('study_program')
            ->delete();

        $assessments = Assessment::query()
            ->whereNotNull('study_program')
            ->with('career')
            ->get();

        foreach ($assessments as $assessment) {
            $definition = $programs[
                $assessment
                    ->career
                    ?->slug
            ] ?? null;

            if (
                ! $definition
                || $assessment->study_program !== $definition['name']
            ) {
                $assessment->delete();

                continue;
            }

            $assessment
                ->questions()
                ->whereHas(
                    'skill',
                    fn ($query) => $query->where(
                        'slug',
                        'not like',
                        $definition['prefix'].'%',
                    ),
                )
                ->delete();

            $assessment->update([
                'title' => 'Assessment Awal '.$definition['name'],
                'description' => 'Kerjakan '
                    .AcademicAssessmentCatalog::QUESTION_LIMIT
                    .' pertanyaan dalam 5 bagian untuk Assessment jurusan '
                    .$definition['name']
                    .'. Seluruh pertanyaan berasal dari kompetensi inti jurusan dan urutannya diacak pada setiap sesi baru.',
                'duration_minutes' => 50,
                'is_active' => true,
            ]);
        }
    }
}
