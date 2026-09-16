<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Skill;
use App\Support\AcademicProgramCatalog;
use App\Support\SkillPathScoringPolicy;
use Illuminate\Database\Seeder;
use RuntimeException;

class CareerSkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = Skill::query()
            ->whereIn(
                'slug',
                AcademicProgramCatalog::allSkillSlugs(),
            )
            ->get()
            ->keyBy('slug');

        foreach (
            AcademicProgramCatalog::programs() as $studyProgram => $program
        ) {
            $career = Career::query()
                ->where(
                    'slug',
                    $program['slug'],
                )
                ->first();

            if (! $career) {
                throw new RuntimeException(
                    'Jurusan '.$studyProgram.' belum tersedia.',
                );
            }

            $sync = [];

            foreach (
                AcademicProgramCatalog::skillSlugs(
                    $studyProgram,
                ) as $skillSlug
            ) {
                $skill = $skills->get(
                    $skillSlug,
                );

                if (! $skill) {
                    throw new RuntimeException(
                        'Skill '.$skillSlug.' untuk jurusan '.$studyProgram.' belum tersedia.',
                    );
                }

                $sync[$skill->id] = [
                    'target_level' => SkillPathScoringPolicy::FUNCTIONAL_LEVEL,
                    'importance_weight' => SkillPathScoringPolicy::DEFAULT_IMPORTANCE_WEIGHT,
                    'is_required' => true,
                ];
            }

            $career
                ->skills()
                ->sync(
                    $sync,
                );
        }
    }
}
