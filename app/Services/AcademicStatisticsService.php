<?php

namespace App\Services;

use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Support\AcademicProgramCatalog;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class AcademicStatisticsService
{
    public readonly int $careers;

    public readonly int $areas;

    public readonly int $topics;

    public readonly int $questions;

    public readonly int $projects;

    public function __construct()
    {
        $catalog = AcademicProgramCatalog::programs();

        $academicCareers = Career::query()
            ->where('is_active', true)
            ->whereIn(
                'slug',
                array_column($catalog, 'slug'),
            )
            ->with('skills:id,slug')
            ->get()
            ->filter(
                fn (Career $career): bool => isset(
                    $catalog[$career->name],
                ) && $catalog[$career->name]['slug'] === $career->slug,
            );

        $careerIds = [];

        $skillIds = [];

        $areaSkillMappings = [];

        $areaCount = 0;

        foreach ($academicCareers as $career) {
            $program = $catalog[$career->name];

            $careerIds[] = $career->id;

            $areaCount += count($program['areas']);

            $allowedSkillSlugs = AcademicProgramCatalog::skillSlugs(
                $career->name,
            );

            foreach ($career->skills as $skill) {
                if (
                    in_array(
                        $skill->slug,
                        $allowedSkillSlugs,
                        true,
                    )
                ) {
                    $skillIds[] = $skill->id;
                }
            }

            foreach ($program['areas'] as $area) {
                $expectedSkills = [];

                foreach ($area['skills'] as $definition) {
                    $expectedSkills[] = $definition['slug'];
                }

                sort($expectedSkills);

                $areaSkillMappings[$career->id][] = $expectedSkills;
            }
        }

        $skillIds = array_values(
            array_unique($skillIds),
        );

        $this->careers = $academicCareers->count();

        $this->areas = $areaCount;

        $this->topics = LearningMaterial::query()
            ->whereIn('skill_id', $skillIds)
            ->where('material_type', 'core')
            ->where('is_active', true)
            ->whereIn('difficulty', [
                'Amatir',
                'Menengah',
                'Ahli',
            ])
            ->distinct()
            ->count('skill_id');

        $this->questions = DB::table(
            'assessment_questions as questions',
        )
            ->join(
                'assessments as assessments',
                'assessments.id',
                '=',
                'questions.assessment_id',
            )
            ->join(
                'careers as careers',
                'careers.id',
                '=',
                'assessments.career_id',
            )
            ->join(
                'career_skill as career_skills',
                function (JoinClause $join): void {
                    $join
                        ->on(
                            'career_skills.career_id',
                            '=',
                            'careers.id',
                        )
                        ->on(
                            'career_skills.skill_id',
                            '=',
                            'questions.skill_id',
                        );
                },
            )
            ->whereIn('careers.id', $careerIds)
            ->whereIn('questions.skill_id', $skillIds)
            ->where('assessments.is_active', true)
            ->whereColumn(
                'assessments.study_program',
                'careers.name',
            )
            ->distinct()
            ->count('questions.id');

        $projects = PortfolioProject::query()
            ->whereIn('career_id', $careerIds)
            ->with('skills:id,slug')
            ->get();

        $projectCount = 0;

        foreach ($projects as $project) {
            $actualSkills = $project
                ->skills
                ->pluck('slug')
                ->sort()
                ->values()
                ->all();

            $expectedAreas = $areaSkillMappings[
                $project->career_id
            ] ?? [];

            foreach ($expectedAreas as $expectedSkills) {
                if ($actualSkills === $expectedSkills) {
                    $projectCount++;

                    break;
                }
            }
        }

        $this->projects = $projectCount;
    }
}
