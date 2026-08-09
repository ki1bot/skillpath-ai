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
            AssessmentSeeder::class,
            PortfolioProjectSeeder::class,
        ]);
    }
}
