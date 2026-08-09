<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class CareerSkillSeeder extends Seeder
{
    public function run(): void
    {
        $maps = [
            'backend-developer' => [
                'programming-fundamentals' => [75, 1.50],
                'git-github' => [65, 1.00],
                'terminal-cli' => [60, 0.85],
                'http-fundamentals' => [80, 1.35],
                'database-fundamentals' => [75, 1.35],
                'sql' => [75, 1.25],
                'php-laravel' => [80, 1.45],
                'rest-api' => [85, 1.50],
                'authentication-authorization' => [75, 1.35],
                'eloquent-orm' => [75, 1.15],
                'validation-error-handling' => [75, 1.15],
                'testing-fundamentals' => [65, 1.00],
                'logging-monitoring' => [60, 0.90],
                'deployment-basics' => [60, 0.90],
                'web-security-basics' => [65, 1.10],
            ],
            'frontend-developer' => [
                'git-github' => [60, 0.90],
                'http-fundamentals' => [70, 1.00],
                'html-semantics' => [80, 1.35],
                'css-responsive' => [80, 1.35],
                'javascript' => [85, 1.50],
                'typescript' => [75, 1.20],
                'react' => [85, 1.50],
                'state-management' => [70, 1.05],
                'accessibility' => [70, 1.10],
                'testing-fundamentals' => [60, 0.90],
                'web-performance' => [65, 0.95],
                'deployment-basics' => [55, 0.80],
            ],
            'data-analyst' => [
                'spreadsheet-analysis' => [75, 1.10],
                'statistics-fundamentals' => [75, 1.35],
                'data-cleaning' => [80, 1.45],
                'database-fundamentals' => [60, 0.90],
                'sql' => [80, 1.45],
                'sql-analytics' => [75, 1.30],
                'python-data' => [70, 1.05],
                'pandas' => [75, 1.20],
                'data-visualization' => [80, 1.35],
                'git-github' => [50, 0.70],
            ],
        ];

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        foreach ($maps as $careerSlug => $mapping) {
            $career = Career::query()
                ->where('slug', $careerSlug)
                ->firstOrFail();

            $sync = [];

            foreach ($mapping as $skillSlug => [$target, $weight]) {
                $skill = $skills->get($skillSlug);

                if (! $skill) {
                    continue;
                }

                $sync[$skill->id] = [
                    'target_level' => $target,
                    'importance_weight' => $weight,
                    'is_required' => $weight >= 0.90,
                ];
            }

            $career->skills()->sync($sync);
        }
    }
}
