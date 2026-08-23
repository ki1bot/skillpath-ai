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
            'sistem-informasi' => [
                'si-sql-data-processing' => [75, 1.25],
                'si-spreadsheet-data-analysis' => [70, 1.10],
                'si-business-intelligence-data-visualization' => [70, 1.15],
                'si-database-management' => [75, 1.25],
                'si-web-development' => [75, 1.20],
                'si-system-analysis-design' => [75, 1.25],
                'si-ui-design' => [65, 1.00],
                'si-wireframing-prototyping' => [65, 1.00],
                'si-user-research' => [65, 1.00],
            ],

            'manajemen' => [
                'man-branding' => [70, 1.10],
                'man-digital-marketing' => [75, 1.20],
                'man-market-research' => [75, 1.20],
                'man-financial-planning' => [70, 1.15],
                'man-financial-analysis' => [75, 1.25],
                'man-investment-management' => [70, 1.10],
                'man-recruitment-selection' => [70, 1.10],
                'man-performance-management' => [75, 1.20],
                'man-talent-management' => [70, 1.10],
            ],

            'teknik-informatika' => [
                'ti-algorithms-data-structures' => [80, 1.35],
                'ti-object-oriented-programming' => [80, 1.30],
                'ti-software-engineering' => [80, 1.30],
                'ti-computer-networks' => [75, 1.20],
                'ti-operating-systems' => [75, 1.20],
                'ti-cybersecurity' => [75, 1.25],
                'ti-machine-learning' => [75, 1.25],
                'ti-data-science' => [75, 1.25],
                'ti-computer-vision' => [70, 1.15],
            ],

            'psikologi' => [
                'psi-employee-behavior' => [70, 1.15],
                'psi-organizational-development' => [70, 1.15],
                'psi-psychological-assessment' => [75, 1.25],
                'psi-counseling-skills' => [75, 1.25],
                'psi-interpersonal-communication' => [75, 1.20],
                'psi-emotional-intelligence' => [70, 1.15],
                'psi-research-methodology' => [75, 1.25],
                'psi-interview-observation' => [70, 1.15],
                'psi-survey-data-analysis' => [75, 1.20],
            ],

            'ilmu-komunikasi' => [
                'ikom-media-relations' => [70, 1.15],
                'ikom-corporate-communication' => [75, 1.20],
                'ikom-crisis-communication' => [75, 1.20],
                'ikom-news-writing' => [75, 1.20],
                'ikom-journalistic-interview' => [70, 1.15],
                'ikom-news-reporting' => [75, 1.20],
                'ikom-content-creation' => [75, 1.20],
                'ikom-social-media-management' => [75, 1.20],
                'ikom-video-production' => [70, 1.10],
            ],

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
                ->where(
                    'slug',
                    $careerSlug,
                )
                ->firstOrFail();

            $sync = [];

            foreach ($mapping as $skillSlug => [$target, $weight]) {
                $skill = $skills->get(
                    $skillSlug,
                );

                if (! $skill) {
                    continue;
                }

                $sync[$skill->id] = [
                    'target_level' => $target,
                    'importance_weight' => $weight,
                    'is_required' => $weight >= 0.90,
                ];
            }

            $career
                ->skills()
                ->sync($sync);
        }
    }
}
