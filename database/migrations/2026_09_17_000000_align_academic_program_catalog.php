<?php

use App\Models\User;
use App\Services\RoadmapService;
use App\Support\AcademicProgramCatalog;
use App\Support\SkillPathScoringPolicy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PROJECT_AREAS = [
        'sales-business-intelligence-dashboard' => [
            'Sistem Informasi',
            'Analisis Data',
        ],
        'build-mini-information-system' => [
            'Sistem Informasi',
            'Pengembangan Sistem',
        ],
        'redesign-digital-product' => [
            'Sistem Informasi',
            'UI/UX',
        ],
        'digital-marketing-campaign' => [
            'Manajemen',
            'Marketing',
        ],
        'financial-health-analysis' => [
            'Manajemen',
            'Keuangan',
        ],
        'recruitment-strategy' => [
            'Manajemen',
            'Human Resources',
        ],
        'software-development-project' => [
            'Teknik Informatika',
            'Pemrograman dan Rekayasa Perangkat Lunak',
        ],
        'company-network-security-simulation' => [
            'Teknik Informatika',
            'Jaringan dan Sistem Komputer',
        ],
        'ai-predictive-project' => [
            'Teknik Informatika',
            'Artificial Intelligence',
        ],
        'mini-computer-architecture-design' => [
            'Sistem Komputer',
            'Arsitektur dan Organisasi Komputer',
        ],
        'smart-iot-system' => [
            'Sistem Komputer',
            'Embedded System dan Internet of Things',
        ],
        'secure-network-design' => [
            'Sistem Komputer',
            'Jaringan dan Keamanan Komputer',
        ],
        'employee-organizational-assessment' => [
            'Psikologi',
            'Psikologi Industri dan Organisasi',
        ],
        'counseling-case-simulation' => [
            'Psikologi',
            'Konseling',
        ],
        'mini-psychological-research' => [
            'Psikologi',
            'Penelitian Psikologi',
        ],
        'crisis-communication-simulation' => [
            'Ilmu Komunikasi',
            'Public Relations',
        ],
        'news-reporting-project' => [
            'Ilmu Komunikasi',
            'Jurnalistik',
        ],
        'digital-content-campaign' => [
            'Ilmu Komunikasi',
            'Digital Media',
        ],
    ];

    public function up(): void
    {
        if (
            ! Schema::hasTable('careers')
            || ! Schema::hasTable('skills')
        ) {
            return;
        }

        $affectedUserIds = [];

        DB::transaction(
            function () use (&$affectedUserIds): void {
                $now = now();
                $canonicalSlugs = AcademicProgramCatalog::allSkillSlugs();

                foreach (
                    AcademicProgramCatalog::programs() as $studyProgram => $program
                ) {
                    $careerId = DB::table('careers')
                        ->where(
                            'slug',
                            $program['slug'],
                        )
                        ->value('id');

                    if ($careerId === null) {
                        continue;
                    }

                    DB::table('careers')
                        ->where(
                            'id',
                            $careerId,
                        )
                        ->update([
                            'name' => $studyProgram,
                            'tagline' => $program['tagline'],
                            'description' => $program['description'],
                            'responsibilities' => json_encode(
                                array_map(
                                    fn (array $area): string => $area['name'],
                                    $program['areas'],
                                ),
                                JSON_UNESCAPED_UNICODE,
                            ),
                            'difficulty' => $program['difficulty'],
                            'accent' => $program['accent'],
                            'is_active' => true,
                            'updated_at' => $now,
                        ]);

                    $skillIds = [];

                    foreach (
                        AcademicProgramCatalog::skillDefinitions(
                            $studyProgram,
                        ) as $definition
                    ) {
                        $skillId = DB::table('skills')
                            ->where(
                                'slug',
                                $definition['slug'],
                            )
                            ->value('id');

                        if ($skillId === null) {
                            $skillId = DB::table('skills')
                                ->insertGetId([
                                    'name' => $definition['name'],
                                    'slug' => $definition['slug'],
                                    'category' => $definition['category'],
                                    'description' => $definition['description'],
                                    'difficulty' => $definition['difficulty'],
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);
                        } else {
                            DB::table('skills')
                                ->where(
                                    'id',
                                    $skillId,
                                )
                                ->update([
                                    'name' => $definition['name'],
                                    'category' => $definition['category'],
                                    'description' => $definition['description'],
                                    'difficulty' => $definition['difficulty'],
                                    'updated_at' => $now,
                                ]);
                        }

                        $skillIds[] = (int) $skillId;
                    }

                    if (
                        ! Schema::hasTable('career_skill')
                        || $skillIds === []
                    ) {
                        continue;
                    }

                    DB::table('career_skill')
                        ->where(
                            'career_id',
                            $careerId,
                        )
                        ->whereNotIn(
                            'skill_id',
                            $skillIds,
                        )
                        ->delete();

                    foreach ($skillIds as $skillId) {
                        DB::table('career_skill')
                            ->updateOrInsert(
                                [
                                    'career_id' => $careerId,
                                    'skill_id' => $skillId,
                                ],
                                [
                                    'target_level' => SkillPathScoringPolicy::FUNCTIONAL_LEVEL,
                                    'importance_weight' => SkillPathScoringPolicy::DEFAULT_IMPORTANCE_WEIGHT,
                                    'is_required' => true,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ],
                            );
                    }
                }

                $legacySkillIds = DB::table('skills')
                    ->where(
                        function ($query) {
                            $query
                                ->where('slug', 'like', 'si-%')
                                ->orWhere('slug', 'like', 'man-%')
                                ->orWhere('slug', 'like', 'ti-%')
                                ->orWhere('slug', 'like', 'sk-%')
                                ->orWhere('slug', 'like', 'psi-%')
                                ->orWhere('slug', 'like', 'ikom-%');
                        },
                    )
                    ->whereNotIn(
                        'slug',
                        $canonicalSlugs,
                    )
                    ->pluck('id')
                    ->map(
                        fn ($id): int => (int) $id,
                    )
                    ->all();

                if (
                    $legacySkillIds !== []
                    && Schema::hasTable('skill_prerequisites')
                ) {
                    DB::table('skill_prerequisites')
                        ->whereIn(
                            'skill_id',
                            $legacySkillIds,
                        )
                        ->orWhereIn(
                            'prerequisite_skill_id',
                            $legacySkillIds,
                        )
                        ->delete();
                }

                if (
                    $legacySkillIds !== []
                    && Schema::hasTable('learning_materials')
                ) {
                    $legacyMaterialIds = DB::table('learning_materials')
                        ->whereIn(
                            'skill_id',
                            $legacySkillIds,
                        )
                        ->pluck('id')
                        ->map(
                            fn ($id): int => (int) $id,
                        )
                        ->all();

                    if (
                        $legacyMaterialIds !== []
                        && Schema::hasTable('roadmaps')
                        && Schema::hasTable('roadmap_items')
                    ) {
                        $affectedUserIds = DB::table('roadmaps')
                            ->join(
                                'roadmap_items',
                                'roadmap_items.roadmap_id',
                                '=',
                                'roadmaps.id',
                            )
                            ->where(
                                'roadmaps.is_active',
                                true,
                            )
                            ->whereIn(
                                'roadmap_items.learning_material_id',
                                $legacyMaterialIds,
                            )
                            ->pluck(
                                'roadmaps.user_id',
                            )
                            ->map(
                                fn ($id): int => (int) $id,
                            )
                            ->unique()
                            ->values()
                            ->all();
                    }

                    DB::table('learning_materials')
                        ->whereIn(
                            'skill_id',
                            $legacySkillIds,
                        )
                        ->update([
                            'is_active' => false,
                            'updated_at' => $now,
                        ]);
                }

                if (
                    Schema::hasTable('portfolio_projects')
                    && Schema::hasTable('portfolio_project_skill')
                ) {
                    foreach (
                        self::PROJECT_AREAS as $projectSlug => [
                            $studyProgram,
                            $areaName,
                        ]
                    ) {
                        $projectId = DB::table('portfolio_projects')
                            ->where(
                                'slug',
                                $projectSlug,
                            )
                            ->value('id');

                        if ($projectId === null) {
                            continue;
                        }

                        $skillSlugs = AcademicProgramCatalog::areaSkillSlugs(
                            $studyProgram,
                            $areaName,
                        );

                        $skillIds = DB::table('skills')
                            ->whereIn(
                                'slug',
                                $skillSlugs,
                            )
                            ->pluck('id')
                            ->map(
                                fn ($id): int => (int) $id,
                            )
                            ->all();

                        if (count($skillIds) !== 3) {
                            continue;
                        }

                        DB::table('portfolio_project_skill')
                            ->where(
                                'portfolio_project_id',
                                $projectId,
                            )
                            ->whereNotIn(
                                'skill_id',
                                $skillIds,
                            )
                            ->delete();

                        foreach ($skillIds as $skillId) {
                            DB::table('portfolio_project_skill')
                                ->updateOrInsert(
                                    [
                                        'portfolio_project_id' => $projectId,
                                        'skill_id' => $skillId,
                                    ],
                                    [
                                        'required_level' => SkillPathScoringPolicy::FUNCTIONAL_LEVEL,
                                        'weight' => SkillPathScoringPolicy::DEFAULT_PROJECT_WEIGHT,
                                        'created_at' => $now,
                                        'updated_at' => $now,
                                    ],
                                );
                        }
                    }
                }
            },
        );

        if ($affectedUserIds === []) {
            return;
        }

        $roadmapService = app(
            RoadmapService::class,
        );

        User::query()
            ->whereIn(
                'id',
                $affectedUserIds,
            )
            ->with('targetCareer')
            ->get()
            ->each(
                function (User $user) use ($roadmapService): void {
                    if (! $user->targetCareer) {
                        return;
                    }

                    $roadmapService->regenerate(
                        $user,
                        'Penyelarasan struktur kemampuan jurusan',
                    );
                },
            );
    }

    public function down(): void {}
};
