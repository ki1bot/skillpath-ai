<?php

namespace App\Support;

final class AcademicAssessmentCatalog
{
    public const QUESTION_LIMIT = 25;

    public const QUESTIONS_PER_SKILL = 3;

    public const SKILLS_PER_PROGRAM = 9;

    public const QUESTION_POOL_SIZE = self::QUESTIONS_PER_SKILL
        * self::SKILLS_PER_PROGRAM;

    private const PROGRAMS = [
        'Sistem Informasi' => [
            'si-sql-data-processing',
            'si-spreadsheet-data-analysis',
            'si-business-intelligence-data-visualization',
            'si-database-management',
            'si-web-development',
            'si-system-analysis-design',
            'si-ui-design',
            'si-wireframing-prototyping',
            'si-user-research',
        ],
        'Manajemen' => [
            'man-branding',
            'man-digital-marketing',
            'man-market-research',
            'man-financial-planning',
            'man-financial-analysis',
            'man-investment-management',
            'man-recruitment-selection',
            'man-performance-management',
            'man-talent-management',
        ],
        'Teknik Informatika' => [
            'ti-algorithms-data-structures',
            'ti-object-oriented-programming',
            'ti-software-engineering',
            'ti-computer-networks',
            'ti-operating-systems',
            'ti-cybersecurity',
            'ti-machine-learning',
            'ti-data-science',
            'ti-computer-vision',
        ],
        'Sistem Komputer' => [
            'sk-computer-architecture',
            'sk-digital-logic',
            'sk-microprocessor-microcontroller',
            'sk-embedded-systems',
            'sk-internet-of-things',
            'sk-sensor-actuator-integration',
            'sk-computer-networks',
            'sk-network-administration',
            'sk-network-security',
        ],
        'Psikologi' => [
            'psi-employee-behavior',
            'psi-organizational-development',
            'psi-psychological-assessment',
            'psi-counseling-skills',
            'psi-interpersonal-communication',
            'psi-emotional-intelligence',
            'psi-research-methodology',
            'psi-interview-observation',
            'psi-survey-data-analysis',
        ],
        'Ilmu Komunikasi' => [
            'ikom-media-relations',
            'ikom-corporate-communication',
            'ikom-crisis-communication',
            'ikom-news-writing',
            'ikom-journalistic-interview',
            'ikom-news-reporting',
            'ikom-content-creation',
            'ikom-social-media-management',
            'ikom-video-production',
        ],
    ];

    /**
     * @return array<string, list<string>>
     */
    public static function programs(): array
    {
        return self::PROGRAMS;
    }

    /**
     * @return list<string>
     */
    public static function skillSlugs(
        string $studyProgram,
    ): array {
        return self::PROGRAMS[
            $studyProgram
        ] ?? [];
    }
}
