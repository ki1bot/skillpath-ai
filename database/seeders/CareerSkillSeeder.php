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
                'si-sql-data-processing',
                'si-spreadsheet-data-analysis',
                'si-business-intelligence-data-visualization',
                'si-data-visualization',
                'si-scenario-based-data-analysis',
                'si-database-management',
                'si-web-development',
                'si-system-analysis-design',
                'si-erd-uml',
                'si-problem-solving',
                'si-ui-design',
                'si-wireframing-prototyping',
                'si-prototyping',
                'si-user-research',
                'si-usability',
            ],
            'manajemen' => [
                'man-branding',
                'man-digital-marketing',
                'man-market-research',
                'man-marketing-strategy',
                'man-campaign-analysis',
                'man-financial-planning',
                'man-financial-analysis',
                'man-financial-ratios',
                'man-investment-management',
                'man-financial-decision-making',
                'man-recruitment-selection',
                'man-candidate-selection',
                'man-interview',
                'man-performance-management',
                'man-talent-management',
            ],
            'teknik-informatika' => [
                'ti-algorithms-data-structures',
                'ti-data-structures',
                'ti-object-oriented-programming',
                'ti-software-engineering',
                'ti-debugging',
                'ti-computer-networks',
                'ti-operating-systems',
                'ti-network-troubleshooting',
                'ti-cybersecurity',
                'ti-system-administration',
                'ti-machine-learning',
                'ti-data-science',
                'ti-statistics',
                'ti-model-evaluation',
                'ti-computer-vision',
            ],
            'sistem-komputer' => [
                'sk-computer-architecture',
                'sk-digital-logic',
                'sk-processor',
                'sk-memory',
                'sk-microprocessor-microcontroller',
                'sk-microcontroller',
                'sk-embedded-systems',
                'sk-internet-of-things',
                'sk-sensor-actuator-integration',
                'sk-actuator',
                'sk-computer-networks',
                'sk-network-administration',
                'sk-network-security',
                'sk-firewall',
                'sk-threat-detection',
            ],
            'psikologi' => [
                'psi-employee-behavior',
                'psi-organizational-behavior',
                'psi-work-style-assessment',
                'psi-psychological-assessment',
                'psi-organizational-development',
                'psi-interpersonal-communication',
                'psi-counseling-skills',
                'psi-empathy',
                'psi-emotional-intelligence',
                'psi-counseling-scenario',
                'psi-research-methodology',
                'psi-interview-observation',
                'psi-observation',
                'psi-survey-data-analysis',
                'psi-data-analysis',
            ],
            'ilmu-komunikasi' => [
                'ikom-media-relations',
                'ikom-corporate-communication',
                'ikom-crisis-communication',
                'ikom-public-communication',
                'ikom-reputation-management',
                'ikom-news-writing',
                'ikom-journalistic-interview',
                'ikom-news-reporting',
                'ikom-fact-checking',
                'ikom-journalistic-ethics',
                'ikom-content-creation',
                'ikom-social-media-management',
                'ikom-video-production',
                'ikom-content-strategy',
                'ikom-audience-analysis',
            ],
        ];

        $skills = Skill::query()
            ->get()
            ->keyBy('slug');

        foreach ($maps as $careerSlug => $skillSlugs) {
            $career = Career::query()
                ->where('slug', $careerSlug)
                ->firstOrFail();

            $sync = [];

            foreach ($skillSlugs as $skillSlug) {
                $skill = $skills->get($skillSlug);

                if (! $skill) {
                    continue;
                }

                $targetLevel = $skill->difficulty === 'Dasar' ? 70 : 75;
                $weight = $skill->difficulty === 'Dasar' ? 1.10 : 1.20;

                $sync[$skill->id] = [
                    'target_level' => $targetLevel,
                    'importance_weight' => $weight,
                    'is_required' => true,
                ];
            }

            $career->skills()->sync($sync);
        }
    }
}
