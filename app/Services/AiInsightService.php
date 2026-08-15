<?php

namespace App\Services;

use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Models\User;
use App\Models\UserProject;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class AiInsightService
{
    /**
     * @param  array<string, mixed>  $readiness
     * @return array{
     *     progress: string|null,
     *     schedule: string|null,
     *     obstacles: string|null,
     *     generated_by_ai: bool,
     *     model: string|null
     * }
     */
    public function progress(
        User $user,
        array $readiness,
    ): array {
        $history = $user->readinessSnapshots()
            ->latest()
            ->limit(5)
            ->get([
                'score',
                'trigger',
                'created_at',
            ])
            ->map(
                fn ($item) => [
                    'score' => (float) $item->score,
                    'trigger' => $item->trigger,
                    'date' => $item
                        ->created_at
                        ?->toDateTimeString(),
                ],
            )
            ->values()
            ->all();

        $evaluations = $user->evaluations()
            ->latest()
            ->limit(5)
            ->get([
                'score',
                'passed',
                'created_at',
            ])
            ->map(
                fn ($item) => [
                    'score' => (float) $item->score,
                    'passed' => (bool) $item->passed,
                    'date' => $item
                        ->created_at
                        ?->toDateTimeString(),
                ],
            )
            ->values()
            ->all();

        $obstacles = $user->progressLogs()
            ->whereNotNull('obstacle')
            ->where(
                'obstacle',
                '!=',
                '',
            )
            ->latest('logged_at')
            ->limit(8)
            ->pluck('obstacle')
            ->filter(
                fn ($value) => is_string($value)
                    && trim($value) !== '',
            )
            ->map(
                fn ($value) => trim(
                    (string) $value,
                ),
            )
            ->values()
            ->all();

        $recentMinutes = (int) $user->progressLogs()
            ->where(
                'logged_at',
                '>=',
                now()->subDays(14),
            )
            ->sum('minutes_spent');

        $roadmap = Roadmap::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->where(
                'is_active',
                true,
            )
            ->with(
                'items.material:id,title,estimated_minutes',
            )
            ->first();

        $nextMaterials = $roadmap
            ?->items
            ->filter(
                fn (RoadmapItem $item) => in_array(
                    $item->status,
                    [
                        'available',
                        'needs_reinforcement',
                    ],
                    true,
                ),
            )
            ->sortBy('position')
            ->take(3)
            ->map(
                fn (RoadmapItem $item) => [
                    'title' => $item
                        ->material
                        ->title,
                    'minutes' => (int) $item
                        ->material
                        ->estimated_minutes,
                ],
            )
            ->values()
            ->all() ?? [];

        $result = $this->ask(
            $user,
            'progress',
            'Kembalikan tepat tiga bagian dengan tag <PROGRESS>, <SCHEDULE>, dan <OBSTACLES>. PROGRESS menjelaskan perkembangan kesiapan berdasarkan data. SCHEDULE memberikan saran pembagian waktu belajar berdasarkan data waktu dan materi yang tersedia. OBSTACLES menjelaskan pola kendala berdasarkan kendala yang benar-benar tercatat. Jangan membuat nilai, skill, materi, progres, kendala, atau fakta baru. Setiap bagian maksimal 90 kata.',
            [
                'readiness' => $readiness,
                'history' => $history,
                'evaluations' => $evaluations,
                'weekly_study_hours' => (int) $user
                    ->weekly_study_hours,
                'recent_minutes_14_days' => $recentMinutes,
                'next_materials' => $nextMaterials,
                'obstacles' => $obstacles,
            ],
            560,
            [
                'PROGRESS',
                'SCHEDULE',
                'OBSTACLES',
            ],
            8,
        );

        if ($result === null) {
            return $this->emptyProgress();
        }

        $sections = $this->sections(
            $result['content'],
        );

        if ($sections === null) {
            return $this->emptyProgress();
        }

        return [
            ...$sections,
            'generated_by_ai' => true,
            'model' => $result['model'],
        ];
    }

    /**
     * @param  array<string, mixed>  $readiness
     * @return array{
     *     content: string|null,
     *     generated_by_ai: bool,
     *     model: string|null
     * }
     */
    public function projectFeedback(
        User $user,
        PortfolioProject $project,
        ?UserProject $userProject,
        array $readiness,
    ): array {
        $result = $this->ask(
            $user,
            'project-'.$project->id,
            'Berikan umpan balik proyek dengan tiga bagian teks: Kekuatan, Risiko, dan Langkah berikutnya. Gunakan hanya deskripsi proyek, kesiapan, progres, dan catatan pengguna yang diberikan. Jangan mengklaim membaca source code atau repository. Jangan membuat progres, fakta, atau kemampuan baru. Maksimal 140 kata.',
            [
                'project' => [
                    'title' => $project->title,
                    'difficulty' => $project->difficulty,
                    'problem_statement' => $project
                        ->problem_statement,
                    'minimum_features' => $project
                        ->minimum_features,
                    'completion_criteria' => $project
                        ->completion_criteria,
                ],
                'readiness' => $readiness,
                'progress' => $userProject
                    ? [
                        'status' => $userProject->status,
                        'percentage' => (int) $userProject
                            ->progress_percentage,
                        'notes' => $userProject->notes,
                    ]
                    : null,
            ],
            340,
            [],
            4,
        );

        return $result
            ?? $this->emptyContent();
    }

    /**
     * @return array{
     *     content: string|null,
     *     generated_by_ai: bool,
     *     model: string|null
     * }
     */
    public function exerciseVariation(
        User $user,
        LearningMaterial $material,
    ): array {
        $result = $this->ask(
            $user,
            'exercise-'.$material->id,
            'Buat tepat tiga variasi latihan bernomor 1, 2, dan 3 berdasarkan practice_task yang diberikan. Variasi pertama lebih sederhana, variasi kedua menekankan bukti atau dokumentasi, dan variasi ketiga menambahkan edge case yang masih berkaitan dengan skill dan materi yang sama. Jangan membuat skill baru. Maksimal 120 kata.',
            [
                'skill' => $material
                    ->skill
                    ?->name,
                'title' => $material->title,
                'difficulty' => $material
                    ->difficulty,
                'objectives' => $material
                    ->learning_objectives,
                'practice_task' => $material
                    ->practice_task,
            ],
            300,
            [],
            4,
        );

        return $result
            ?? $this->emptyContent();
    }

    /**
     * @param  array<string, mixed>  $context
     * @param  array<int, string>  $requiredTags
     * @return array{
     *     content: string,
     *     generated_by_ai: true,
     *     model: string
     * }|null
     */
    private function ask(
        User $user,
        string $scope,
        string $task,
        array $context,
        int $maxTokens,
        array $requiredTags = [],
        int $timeoutSeconds = 4,
    ): ?array {
        $key = config(
            'services.openrouter.key',
        );

        $model = config(
            'services.openrouter.model',
            'openrouter/free',
        );

        $baseUrl = config(
            'services.openrouter.base_url',
            'https://openrouter.ai/api/v1',
        );

        $configuredFallbackModels = config(
            'services.openrouter.fallback_models',
            [],
        );

        if (
            ! is_string($key)
            || trim($key) === ''
            || ! is_string($model)
            || trim($model) === ''
            || ! is_string($baseUrl)
            || trim($baseUrl) === ''
        ) {
            return null;
        }

        $models = [
            trim($model),
        ];

        if (is_array($configuredFallbackModels)) {
            foreach ($configuredFallbackModels as $fallbackModel) {
                if (
                    ! is_string($fallbackModel)
                    || trim($fallbackModel) === ''
                ) {
                    continue;
                }

                $models[] = trim($fallbackModel);
            }
        }

        $models = array_values(
            array_unique($models),
        );

        $json = json_encode(
            $context,
            JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES,
        );

        if (! is_string($json)) {
            return null;
        }

        $cacheKey = 'skillpath-ai-insight:v5:'
            .$scope
            .':'
            .$user->id
            .':'
            .sha1(
                $baseUrl
                    .'|'
                    .implode('|', $models)
                    .'|'
                    .$json,
            );

        $cached = Cache::get(
            $cacheKey,
        );

        if (
            is_array($cached)
            && is_string(
                $cached['content'] ?? null,
            )
            && is_string(
                $cached['model'] ?? null,
            )
            && $this->looksIndonesian(
                $cached['content'],
            )
            && $this->hasRequiredTags(
                $cached['content'],
                $requiredTags,
            )
        ) {
            return [
                'content' => $cached['content'],
                'generated_by_ai' => true,
                'model' => $cached['model'],
            ];
        }

        foreach ($models as $candidateModel) {
            try {
                $response = Http::withToken(
                    $key,
                )
                    ->acceptJson()
                    ->asJson()
                    ->connectTimeout(2)
                    ->timeout($timeoutSeconds)
                    ->post(
                        rtrim(
                            $baseUrl,
                            '/',
                        ).'/chat/completions',
                        [
                            'model' => $candidateModel,
                            'messages' => [
                                [
                                    'role' => 'system',
                                    'content' => 'Anda adalah fitur AI SkillPath AI. Locale aplikasi saat ini adalah '.app()->getLocale().'. Seluruh teks yang ditampilkan kepada pengguna wajib menggunakan Bahasa Indonesia. Jangan menulis kalimat dalam Bahasa Inggris. Nama teknologi, framework, API, database, bahasa pemrograman, library, atau istilah teknis yang umum boleh tetap menggunakan nama aslinya. Gunakan hanya data yang diberikan. Jangan mengubah skor, hasil asesmen, status progres, keputusan roadmap, kemampuan, proyek, materi, atau fakta lain. Jangan membuat data yang tidak diberikan. Gunakan Bahasa Indonesia yang alami, jelas, dan ringkas. '.$task,
                                ],
                                [
                                    'role' => 'user',
                                    'content' => $json,
                                ],
                            ],
                            'temperature' => 0.2,
                            'max_tokens' => $maxTokens,
                            'provider' => [
                                'allow_fallbacks' => true,
                            ],
                        ],
                    );

                if (! $response->successful()) {
                    continue;
                }

                $content = $response->json(
                    'choices.0.message.content',
                );

                if (! is_string($content)) {
                    continue;
                }

                $content = $this->normalizeContent(
                    $content,
                );

                if (
                    $content === null
                    || ! $this->looksIndonesian(
                        $content,
                    )
                    || ! $this->hasRequiredTags(
                        $content,
                        $requiredTags,
                    )
                ) {
                    continue;
                }

                $responseModel = $response->json(
                    'model',
                );

                $resolvedModel = is_string(
                    $responseModel,
                )
                    && trim($responseModel) !== ''
                        ? trim($responseModel)
                        : $candidateModel;

                Cache::put(
                    $cacheKey,
                    [
                        'content' => $content,
                        'model' => $resolvedModel,
                    ],
                    now()->addHours(12),
                );

                return [
                    'content' => $content,
                    'generated_by_ai' => true,
                    'model' => $resolvedModel,
                ];
            } catch (Throwable) {
                continue;
            }
        }

        return null;
    }

    private function normalizeContent(
        string $content,
    ): ?string {
        $content = trim(
            $content,
        );

        if ($content === '') {
            return null;
        }

        $content = str_replace(
            [
                '```',
                '**',
                '__',
                '`',
            ],
            '',
            $content,
        );

        $content = preg_replace(
            '/[ \t]+/u',
            ' ',
            $content,
        );

        if (! is_string($content)) {
            return null;
        }

        $content = preg_replace(
            "/\n{3,}/u",
            "\n\n",
            $content,
        );

        if (! is_string($content)) {
            return null;
        }

        $content = trim(
            $content,
        );

        return $content !== ''
            ? $content
            : null;
    }

    private function looksIndonesian(
        string $text,
    ): bool {
        $text = Str::lower(
            strip_tags($text),
        );

        $indonesianMatches = [];
        $englishMatches = [];

        $indonesianCount = preg_match_all(
            '/\b(?:yang|dan|untuk|dengan|dari|pada|adalah|karena|anda|kamu|kemampuan|belajar|prioritas|utama|saat|masih|selisih|setelah|perlu|berikutnya|kesiapan|proyek|materi|kendala|waktu|nilai|penguatan|risiko|langkah|evaluasi|jadwal|progres|perkembangan|berhasil|dibuat|dikelompokkan|kekuatan|sudah|ditingkatkan|kerjakan|tugas|hasil|buat|tambahkan|latihan|bukti|pengguna|mingguan|menit|sesi|catatan|hambatan|berikut|tercatat|gunakan|memiliki|menjadi|berada|dapat|belum|lebih|sesuai|bagian|karier|pemahaman|dipelajari|diselesaikan|dikerjakan)\b/u',
            $text,
            $indonesianMatches,
        );

        $englishCount = preg_match_all(
            '/\b(?:the|and|for|with|from|this|that|your|you|is|are|was|were|to|of|in|on|because|after|before|current|learning|should|needs|need|next|improve|improved|based|using|use|has|have|still|result|results|successfully|created|grouped|development|strength|risk|step|steps|weekly|minutes|recorded|project|progress|schedule|obstacles)\b/u',
            $text,
            $englishMatches,
        );

        if (
            ! is_int($indonesianCount)
            || ! is_int($englishCount)
        ) {
            return false;
        }

        return $indonesianCount >= 2
            && $indonesianCount > $englishCount;
    }

    /**
     * @param  array<int, string>  $requiredTags
     */
    private function hasRequiredTags(
        string $content,
        array $requiredTags,
    ): bool {
        foreach ($requiredTags as $tag) {
            if (
                ! str_contains(
                    $content,
                    '<'.$tag.'>',
                )
                || ! str_contains(
                    $content,
                    '</'.$tag.'>',
                )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{
     *     progress: string,
     *     schedule: string,
     *     obstacles: string
     * }|null
     */
    private function sections(
        string $content,
    ): ?array {
        $result = [];

        foreach (
            [
                'progress' => 'PROGRESS',
                'schedule' => 'SCHEDULE',
                'obstacles' => 'OBSTACLES',
            ] as $key => $tag
        ) {
            if (
                preg_match(
                    '/<'
                        .$tag
                        .'>\s*(.*?)\s*<\/'
                        .$tag
                        .'>/si',
                    $content,
                    $matches,
                ) !== 1
            ) {
                return null;
            }

            $value = trim(
                (string) $matches[1],
            );

            if ($value === '') {
                return null;
            }

            $result[$key] = $value;
        }

        return [
            'progress' => $result['progress'],
            'schedule' => $result['schedule'],
            'obstacles' => $result['obstacles'],
        ];
    }

    /**
     * @return array{
     *     progress: null,
     *     schedule: null,
     *     obstacles: null,
     *     generated_by_ai: false,
     *     model: null
     * }
     */
    private function emptyProgress(): array
    {
        return [
            'progress' => null,
            'schedule' => null,
            'obstacles' => null,
            'generated_by_ai' => false,
            'model' => null,
        ];
    }

    /**
     * @return array{
     *     content: null,
     *     generated_by_ai: false,
     *     model: null
     * }
     */
    private function emptyContent(): array
    {
        return [
            'content' => null,
            'generated_by_ai' => false,
            'model' => null,
        ];
    }
}
