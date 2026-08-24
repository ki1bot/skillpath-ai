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
                'si-sql-data-processing' => [75, 1.25, true],
                'si-spreadsheet-data-analysis' => [70, 1.10, true],
                'si-business-intelligence-data-visualization' => [70, 1.15, true],
                'si-database-management' => [75, 1.25, true],
                'si-web-development' => [75, 1.20, true],
                'si-system-analysis-design' => [75, 1.25, true],
                'si-ui-design' => [65, 1.00, true],
                'si-wireframing-prototyping' => [65, 1.00, true],
                'si-user-research' => [65, 1.00, true],
            ],
            'manajemen' => [
                'man-branding' => [70, 1.10, true],
                'man-digital-marketing' => [75, 1.20, true],
                'man-market-research' => [75, 1.20, true],
                'man-financial-planning' => [70, 1.15, true],
                'man-financial-analysis' => [75, 1.25, true],
                'man-investment-management' => [70, 1.10, true],
                'man-recruitment-selection' => [70, 1.10, true],
                'man-performance-management' => [75, 1.20, true],
                'man-talent-management' => [70, 1.10, true],
            ],
            'teknik-informatika' => [
                'ti-algorithms-data-structures' => [80, 1.35, true],
                'ti-object-oriented-programming' => [80, 1.30, true],
                'ti-software-engineering' => [80, 1.30, true],
                'ti-computer-networks' => [75, 1.20, true],
                'ti-operating-systems' => [75, 1.20, true],
                'ti-cybersecurity' => [75, 1.25, true],
                'ti-machine-learning' => [75, 1.25, true],
                'ti-data-science' => [75, 1.25, true],
                'ti-computer-vision' => [70, 1.15, true],
            ],
            'sistem-komputer' => [
                'sk-computer-architecture' => [80, 1.30, true],
                'sk-digital-logic' => [80, 1.30, true],
                'sk-microprocessor-microcontroller' => [75, 1.25, true],
                'sk-embedded-systems' => [80, 1.30, true],
                'sk-internet-of-things' => [75, 1.25, true],
                'sk-sensor-actuator-integration' => [75, 1.20, true],
                'sk-computer-networks' => [75, 1.20, true],
                'sk-network-administration' => [75, 1.20, true],
                'sk-network-security' => [80, 1.30, true],
            ],
            'psikologi' => [
                'psi-employee-behavior' => [70, 1.15, true],
                'psi-organizational-development' => [70, 1.15, true],
                'psi-psychological-assessment' => [75, 1.25, true],
                'psi-counseling-skills' => [75, 1.25, true],
                'psi-interpersonal-communication' => [75, 1.20, true],
                'psi-emotional-intelligence' => [70, 1.15, true],
                'psi-research-methodology' => [75, 1.25, true],
                'psi-interview-observation' => [70, 1.15, true],
                'psi-survey-data-analysis' => [75, 1.20, true],
            ],
            'ilmu-komunikasi' => [
                'ikom-media-relations' => [70, 1.15, true],
                'ikom-corporate-communication' => [75, 1.20, true],
                'ikom-crisis-communication' => [75, 1.20, true],
                'ikom-news-writing' => [75, 1.20, true],
                'ikom-journalistic-interview' => [70, 1.15, true],
                'ikom-news-reporting' => [75, 1.20, true],
                'ikom-content-creation' => [75, 1.20, true],
                'ikom-social-media-management' => [75, 1.20, true],
                'ikom-video-production' => [70, 1.10, true],
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

            foreach (
                $mapping as $skillSlug => [
                    $target,
                    $weight,
                    $isRequired,
                ]
            ) {
                $skill = $skills->get(
                    $skillSlug,
                );

                if (! $skill) {
                    continue;
                }

                $sync[$skill->id] = [
                    'target_level' => $target,
                    'importance_weight' => $weight,
                    'is_required' => $isRequired,
                ];
            }

            $career
                ->skills()
                ->sync($sync);
        }
    }
}
