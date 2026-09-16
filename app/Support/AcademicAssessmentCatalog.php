<?php

namespace App\Support;

final class AcademicAssessmentCatalog
{
    public const BASE_QUESTIONS_PER_SKILL = 5;

    public const MAX_QUESTIONS_PER_SKILL = 6;

    public const QUESTIONS_PER_SKILL = self::BASE_QUESTIONS_PER_SKILL;

    public const SKILLS_PER_PROGRAM = 9;

    public const BASE_QUESTION_POOL_SIZE = self::BASE_QUESTIONS_PER_SKILL
        * self::SKILLS_PER_PROGRAM;

    public const SUPPLEMENTAL_QUESTION_COUNT = 5;

    public const QUESTION_POOL_SIZE = self::BASE_QUESTION_POOL_SIZE
        + self::SUPPLEMENTAL_QUESTION_COUNT;

    public const QUESTION_LIMIT = self::QUESTION_POOL_SIZE;

    private const SUPPLEMENTAL_SKILLS = [
        'Sistem Informasi' => [
            'si-sql-data-processing',
            'si-database-management',
            'si-web-development',
            'si-system-analysis-design',
            'si-ui-design',
        ],
        'Manajemen' => [
            'man-branding',
            'man-digital-marketing',
            'man-financial-planning',
            'man-recruitment-selection',
            'man-performance-management',
        ],
        'Teknik Informatika' => [
            'ti-algorithms-data-structures',
            'ti-software-engineering',
            'ti-computer-networks',
            'ti-cybersecurity',
            'ti-machine-learning',
        ],
        'Sistem Komputer' => [
            'sk-computer-architecture',
            'sk-embedded-systems',
            'sk-internet-of-things',
            'sk-computer-networks',
            'sk-network-security',
        ],
        'Psikologi' => [
            'psi-employee-behavior',
            'psi-psychological-assessment',
            'psi-counseling-skills',
            'psi-research-methodology',
            'psi-survey-data-analysis',
        ],
        'Ilmu Komunikasi' => [
            'ikom-media-relations',
            'ikom-crisis-communication',
            'ikom-news-writing',
            'ikom-content-creation',
            'ikom-social-media-management',
        ],
    ];

    public static function programs(): array
    {
        $programs = [];

        foreach (
            array_keys(
                AcademicProgramCatalog::programs(),
            ) as $studyProgram
        ) {
            $programs[$studyProgram] = AcademicProgramCatalog::skillSlugs(
                $studyProgram,
            );
        }

        return $programs;
    }

    public static function skillSlugs(string $studyProgram): array
    {
        return AcademicProgramCatalog::skillSlugs(
            $studyProgram,
        );
    }

    public static function supplementalSkillSlugs(string $studyProgram): array
    {
        return self::SUPPLEMENTAL_SKILLS[$studyProgram] ?? [];
    }

    public static function questionCapacityForSkill(
        string $studyProgram,
        string $skillSlug,
    ): int {
        return in_array(
            $skillSlug,
            self::supplementalSkillSlugs($studyProgram),
            true,
        )
            ? self::MAX_QUESTIONS_PER_SKILL
            : self::BASE_QUESTIONS_PER_SKILL;
    }
}
