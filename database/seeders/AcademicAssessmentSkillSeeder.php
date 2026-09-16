<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Support\AcademicProgramCatalog;
use Illuminate\Database\Seeder;

class AcademicAssessmentSkillSeeder extends Seeder
{
    public function run(): void
    {
        foreach (
            AcademicProgramCatalog::skillDefinitions() as $definition
        ) {
            Skill::updateOrCreate(
                [
                    'slug' => $definition['slug'],
                ],
                [
                    'name' => $definition['name'],
                    'category' => $definition['category'],
                    'description' => $definition['description'],
                    'difficulty' => $definition['difficulty'],
                ],
            );
        }
    }
}
