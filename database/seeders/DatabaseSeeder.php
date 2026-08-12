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
            SkillSeeder::class,
            CareerSkillSeeder::class,
            SkillPrerequisiteSeeder::class,
            LearningMaterialSeeder::class,
            ReinforcementMaterialSeeder::class,
            AssessmentSeeder::class,
            AssessmentEnhancementSeeder::class,
            PortfolioProjectSeeder::class,
        ]);
    }
}
