<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Roadmap;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicProgramTransitionSeeder extends Seeder
{
    private const LEGACY_SKILL_SLUGS = [
        'programming-fundamentals',
        'data-structures-algorithms',
        'git-github',
        'terminal-cli',
        'http-fundamentals',
        'database-fundamentals',
        'sql',
        'database-performance',
        'testing-fundamentals',
        'deployment-basics',
        'php-laravel',
        'rest-api',
        'api-documentation',
        'authentication-authorization',
        'eloquent-orm',
        'validation-error-handling',
        'logging-monitoring',
        'caching-strategies',
        'web-security-basics',
        'html-semantics',
        'css-responsive',
        'javascript',
        'browser-dom-events',
        'typescript',
        'react',
        'component-architecture',
        'state-management',
        'frontend-testing',
        'accessibility',
        'web-performance',
        'spreadsheet-analysis',
        'statistics-fundamentals',
        'business-metrics-kpi',
        'data-cleaning',
        'exploratory-data-analysis',
        'python-data',
        'pandas',
        'data-visualization',
        'data-storytelling',
        'sql-analytics',
    ];

    public function run(): void
    {
        $legacyCareerIds = Career::query()
            ->where(
                function ($query) {
                    $query
                        ->where(
                            'difficulty',
                            'Legacy',
                        )
                        ->orWhere(
                            'tagline',
                            'Data lama SkillPath.',
                        );
                },
            )
            ->pluck('id');

        if ($legacyCareerIds->isNotEmpty()) {
            $programs = Career::query()
                ->whereIn(
                    'slug',
                    [
                        'sistem-informasi',
                        'manajemen',
                        'teknik-informatika',
                        'sistem-komputer',
                        'psikologi',
                        'ilmu-komunikasi',
                    ],
                )
                ->where(
                    'is_active',
                    true,
                )
                ->get()
                ->keyBy('name');

            foreach ($programs as $studyProgram => $career) {
                $userIds = User::query()
                    ->whereIn(
                        'target_career_id',
                        $legacyCareerIds,
                    )
                    ->where(
                        'study_program',
                        $studyProgram,
                    )
                    ->pluck('id');

                if ($userIds->isEmpty()) {
                    continue;
                }

                User::query()
                    ->whereIn(
                        'id',
                        $userIds,
                    )
                    ->update([
                        'target_career_id' => $career->id,
                    ]);

                Roadmap::query()
                    ->whereIn(
                        'user_id',
                        $userIds,
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->update([
                        'is_active' => false,
                    ]);
            }

            $unresolvedUserIds = User::query()
                ->whereIn(
                    'target_career_id',
                    $legacyCareerIds,
                )
                ->pluck('id');

            if ($unresolvedUserIds->isNotEmpty()) {
                User::query()
                    ->whereIn(
                        'id',
                        $unresolvedUserIds,
                    )
                    ->update([
                        'target_career_id' => null,
                        'onboarding_completed_at' => null,
                    ]);

                Roadmap::query()
                    ->whereIn(
                        'user_id',
                        $unresolvedUserIds,
                    )
                    ->where(
                        'is_active',
                        true,
                    )
                    ->update([
                        'is_active' => false,
                    ]);
            }

            Career::query()
                ->whereIn(
                    'id',
                    $legacyCareerIds,
                )
                ->delete();
        }

        Skill::query()
            ->whereIn(
                'slug',
                self::LEGACY_SKILL_SLUGS,
            )
            ->delete();
    }
}
