<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            CareerSeeder::class,
            AcademicProgramTransitionSeeder::class,
            AcademicAssessmentSkillSeeder::class,
            CareerSkillSeeder::class,
            AcademicAssessmentSeeder::class,
            AcademicAssessmentQuestionPoolSeeder::class,
            AcademicAssessmentCleanupSeeder::class,
            AcademicProgramLearningMaterialSeeder::class,
            AcademicPortfolioProjectSeeder::class,
        ]);
    }
}
