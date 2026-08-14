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
     *     progress: string,
     *     schedule: string,
     *     obstacles: string,
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
            ->map(fn ($item) => [
                'score' => (float) $item->score,
                'trigger' => $item->trigger,
                'date' => $item->created_at?->toDateTimeString(),
            ])
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
            ->map(fn ($item) => [
                'score' => (float) $item->score,
                'passed' => (bool) $item->passed,
                'date' => $item->created_at?->toDateTimeString(),
            ])
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
                    'title' => $item->material->title,
                    'minutes' => (int) $item->material->estimated_minutes,
                ],
            )
            ->values()
            ->all() ?? [];

        $fallback = [
            'progress' => $this->progressFallback(
                $readiness,
                $history,
                $recentMinutes,
            ),
            'schedule' => $this->scheduleFallback(
                (int) $user->weekly_study_hours,
                $nextMaterials,
            ),
            'obstacles' => $this->obstacleFallback(
                $obstacles,
            ),
        ];

        $result = $this->ask(
            $user,
            'progress',
            'Kembalikan tepat tiga bagian dengan tag <PROGRESS>, <SCHEDULE>, dan <OBSTACLES>. PROGRESS merangkum perubahan kesiapan dan evaluasi. SCHEDULE membagi waktu belajar mingguan berdasarkan materi terbuka. OBSTACLES mengelompokkan kendala yang benar-benar tercatat. Jangan membuat nilai, skill, atau materi baru. Masing-masing bagian maksimal 90 kata.',
            [
                'readiness' => $readiness,
                'history' => $history,
                'evaluations' => $evaluations,
                'weekly_study_hours' => (int) $user->weekly_study_hours,
                'recent_minutes_14_days' => $recentMinutes,
                'next_materials' => $nextMaterials,
                'obstacles' => $obstacles,
            ],
            560,
        );

        if ($result === null) {
            return [
                ...$fallback,
                'generated_by_ai' => false,
                'model' => null,
            ];
        }

        $sections = $this->sections(
            $result['content'],
        );

        if ($sections === null) {
            return [
                ...$fallback,
                'generated_by_ai' => false,
                'model' => null,
            ];
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
     *     content: string,
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
        $fallback = $this->projectFallback(
            $project,
            $userProject,
            $readiness,
        );

        $result = $this->ask(
            $user,
            'project-'.$project->id,
            'Berikan umpan balik proyek dengan tiga bagian: Kekuatan, Risiko, Langkah berikutnya. Gunakan hanya deskripsi proyek, kesiapan, progres, dan catatan pengguna. Jangan mengklaim membaca source code atau repository. Maksimal 140 kata.',
            [
                'project' => [
                    'title' => $project->title,
                    'difficulty' => $project->difficulty,
                    'problem_statement' => $project->problem_statement,
                    'minimum_features' => $project->minimum_features,
                    'completion_criteria' => $project->completion_criteria,
                ],
                'readiness' => $readiness,
                'progress' => $userProject
                    ? [
                        'status' => $userProject->status,
                        'percentage' => (int) $userProject->progress_percentage,
                        'notes' => $userProject->notes,
                    ]
                    : null,
            ],
            340,
        );

        return $result ?? [
            'content' => $fallback,
            'generated_by_ai' => false,
            'model' => null,
        ];
    }

    /**
     * @return array{
     *     content: string,
     *     generated_by_ai: bool,
     *     model: string|null
     * }
     */
    public function exerciseVariation(
        User $user,
        LearningMaterial $material,
    ): array {
        $fallback = "1. Variasi dasar — Kerjakan versi paling kecil dari tugas: {$material->practice_task}\n"
            ."2. Variasi bukti — Ulangi tugas yang sama dan dokumentasikan langkah, hasil, serta satu keputusan penting.\n"
            ."3. Variasi tantangan — Tambahkan satu edge case yang masih berada dalam konteks {$material->skill?->name}.";

        $result = $this->ask(
            $user,
            'exercise-'.$material->id,
            'Buat tepat tiga variasi latihan bernomor 1, 2, 3 dari practice_task yang tersedia. Variasi pertama lebih sederhana, kedua meminta bukti atau dokumentasi, ketiga menambahkan edge case. Jangan menambah skill baru. Maksimal 120 kata.',
            [
                'skill' => $material->skill?->name,
                'title' => $material->title,
                'difficulty' => $material->difficulty,
                'objectives' => $material->learning_objectives,
                'practice_task' => $material->practice_task,
            ],
            300,
        );

        return $result ?? [
            'content' => $fallback,
            'generated_by_ai' => false,
            'model' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $context
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

        $json = json_encode(
            $context,
            JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES,
        );

        if (! is_string($json)) {
            return null;
        }

        $cacheKey = 'skillpath-ai-insight:v3:'
            .$scope
            .':'
            .$user->id
            .':'
            .sha1(
                $baseUrl
                    .'|'
                    .$model
                    .'|'
                    .$json,
            );

        $failureCacheKey = $cacheKey
            .':unavailable';

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
        ) {
            return [
                'content' => $cached['content'],
                'generated_by_ai' => true,
                'model' => $cached['model'],
            ];
        }

        if (
            Cache::get(
                $failureCacheKey,
            ) === true
        ) {
            return null;
        }

        try {
            $response = Http::withToken(
                $key,
            )
                ->acceptJson()
                ->asJson()
                ->connectTimeout(1)
                ->timeout(2)
                ->post(
                    rtrim(
                        $baseUrl,
                        '/',
                    ).'/chat/completions',
                    [
                        'model' => $model,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'Anda adalah fitur AI pendukung SkillPath AI untuk pengguna Indonesia. Seluruh kalimat yang Anda hasilkan WAJIB menggunakan Bahasa Indonesia. Jangan menulis kalimat dalam Bahasa Inggris. Nama teknologi, framework, bahasa pemrograman, API, database, dan istilah teknis yang lazim boleh tetap menggunakan nama aslinya. Gunakan hanya data internal yang diberikan. Jangan mengubah skor, hasil asesmen, status progres, atau keputusan roadmap. Jika data tidak cukup, jelaskan keterbatasannya dalam Bahasa Indonesia. Gunakan bahasa yang ringkas, alami, jelas, dan mudah dipahami. '
                                    .$task,
                            ],
                            [
                                'role' => 'user',
                                'content' => 'Jawab seluruh penjelasan menggunakan Bahasa Indonesia.'
                                    ."\nData internal SkillPath AI: "
                                    .$json,
                            ],
                        ],
                        'temperature' => 0.2,
                        'max_tokens' => $maxTokens,
                    ],
                );

            if (! $response->successful()) {
                $this->markUnavailable(
                    $failureCacheKey,
                );

                return null;
            }

            $content = $response->json(
                'choices.0.message.content',
            );

            if (
                ! is_string($content)
                || trim($content) === ''
            ) {
                $this->markUnavailable(
                    $failureCacheKey,
                );

                return null;
            }

            $content = trim(
                $content,
            );

            if (
                ! $this->looksIndonesian(
                    $content,
                )
            ) {
                $this->markUnavailable(
                    $failureCacheKey,
                );

                return null;
            }

            $responseModel = $response->json(
                'model',
            );

            $resolvedModel = is_string(
                $responseModel,
            )
                && trim($responseModel) !== ''
                    ? trim($responseModel)
                    : $model;

            Cache::put(
                $cacheKey,
                [
                    'content' => $content,
                    'model' => $resolvedModel,
                ],
                now()->addHours(12),
            );

            Cache::forget(
                $failureCacheKey,
            );

            return [
                'content' => $content,
                'generated_by_ai' => true,
                'model' => $resolvedModel,
            ];
        } catch (Throwable) {
            $this->markUnavailable(
                $failureCacheKey,
            );

            return null;
        }
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
            '/\b(?:yang|dan|untuk|dengan|dari|pada|adalah|karena|anda|kamu|kemampuan|belajar|prioritas|utama|saat|masih|selisih|setelah|perlu|berikutnya|kesiapan|proyek|materi|kendala|waktu|nilai|penguatan|risiko|langkah|evaluasi|jadwal|progres|perkembangan|berhasil|dibuat|dikelompokkan|kekuatan|sudah|ditingkatkan|kerjakan|tugas|hasil|buat|tambahkan|latihan|bukti|pengguna|mingguan|menit|sesi|catatan|hambatan|berikut|tercatat|gunakan|memiliki|menjadi|berada|dapat|belum|lebih|sesuai|bagian)\b/u',
            $text,
            $indonesianMatches,
        );

        $englishCount = preg_match_all(
            '/\b(?:the|and|for|with|from|this|that|your|you|is|are|was|were|to|of|in|on|because|after|before|current|learning|should|needs|need|next|improve|improved|based|using|use|has|have|still|result|results|successfully|created|grouped|development|strength|risk|step|steps|weekly|minutes|recorded)\b/u',
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

    private function markUnavailable(
        string $failureCacheKey,
    ): void {
        Cache::put(
            $failureCacheKey,
            true,
            now()->addMinutes(10),
        );
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
     * @param  array<string, mixed>  $readiness
     * @param  array<int, array<string, mixed>>  $history
     */
    private function progressFallback(
        array $readiness,
        array $history,
        int $recentMinutes,
    ): string {
        $current = (float) (
            $readiness['score'] ?? 0
        );

        $oldest = end(
            $history,
        );

        $oldestScore = is_array(
            $oldest,
        )
            ? (float) (
                $oldest['score']
                ?? $current
            )
            : $current;

        $delta = round(
            $current - $oldestScore,
            1,
        );

        $change = $delta > 0
            ? "naik {$delta} poin"
            : (
                $delta < 0
                    ? 'turun '.abs($delta).' poin'
                    : 'belum berubah'
            );

        return "Kesiapan karier saat ini {$current}/100 dan {$change} dibanding snapshot tertua yang tersimpan. Dalam 14 hari terakhir tercatat {$recentMinutes} menit belajar. Gunakan evaluasi dan proyek sebagai bukti utama untuk meningkatkan kesiapan berikutnya.";
    }

    /**
     * @param  array<int, array<string, mixed>>  $materials
     */
    private function scheduleFallback(
        int $weeklyHours,
        array $materials,
    ): string {
        $weeklyMinutes = max(
            $weeklyHours * 60,
            60,
        );

        $sessions = min(
            max(
                (int) ceil(
                    $weeklyMinutes / 120,
                ),
                2,
            ),
            6,
        );

        $perSession = max(
            (int) floor(
                $weeklyMinutes / $sessions,
            ),
            30,
        );

        $titles = collect(
            $materials,
        )
            ->pluck('title')
            ->filter()
            ->implode(', ');

        return $titles === ''
            ? "Bagi waktu belajar menjadi sekitar {$sessions} sesi per minggu dengan durasi sekitar {$perSession} menit. Belum ada materi terbuka, jadi gunakan sesi untuk evaluasi, proyek, atau meninjau hasil assesment."
            : "Bagi waktu belajar menjadi sekitar {$sessions} sesi per minggu dengan durasi sekitar {$perSession} menit. Prioritaskan sesuai urutan roadmap: {$titles}. Sisakan satu sesi pendek untuk evaluasi atau penguatan.";
    }

    /**
     * @param  array<int, string>  $obstacles
     */
    private function obstacleFallback(
        array $obstacles,
    ): string {
        if ($obstacles === []) {
            return 'Belum ada kendala yang dicatat. Isi kolom kendala saat menyimpan progres agar sistem dapat menemukan pola hambatan.';
        }

        $groups = [
            'teknis' => 0,
            'konsep' => 0,
            'waktu' => 0,
            'lainnya' => 0,
        ];

        foreach ($obstacles as $obstacle) {
            $text = Str::lower(
                $obstacle,
            );

            if (
                Str::contains(
                    $text,
                    [
                        'error',
                        'bug',
                        'server',
                        'database',
                        'api',
                        'install',
                        'config',
                    ],
                )
            ) {
                $groups['teknis']++;
            } elseif (
                Str::contains(
                    $text,
                    [
                        'bingung',
                        'paham',
                        'konsep',
                        'logika',
                        'materi',
                        'query',
                    ],
                )
            ) {
                $groups['konsep']++;
            } elseif (
                Str::contains(
                    $text,
                    [
                        'waktu',
                        'sibuk',
                        'jadwal',
                        'deadline',
                        'capek',
                    ],
                )
            ) {
                $groups['waktu']++;
            } else {
                $groups['lainnya']++;
            }
        }

        arsort(
            $groups,
        );

        $top = array_key_first(
            $groups,
        );

        $count = $groups[$top];

        return 'Dari '
            .count($obstacles)
            ." kendala terakhir, pola terbesar adalah {$top} ({$count} catatan). Pilih satu tindakan konkret untuk kategori ini pada sesi berikutnya lalu catat apakah hambatan yang sama masih muncul.";
    }

    /**
     * @param  array<string, mixed>  $readiness
     */
    private function projectFallback(
        PortfolioProject $project,
        ?UserProject $userProject,
        array $readiness,
    ): string {
        $requirementsValue = $readiness[
            'requirements'
        ] ?? [];

        $missingNames = [];

        if (is_array($requirementsValue)) {
            foreach ($requirementsValue as $requirement) {
                if (! is_array($requirement)) {
                    continue;
                }

                $ready = (bool) (
                    $requirement['ready']
                    ?? false
                );

                if ($ready) {
                    continue;
                }

                $name = $requirement['name']
                    ?? null;

                if (! is_string($name)) {
                    continue;
                }

                $name = trim(
                    $name,
                );

                if ($name === '') {
                    continue;
                }

                $missingNames[] = $name;

                if (count($missingNames) >= 3) {
                    break;
                }
            }
        }

        $missing = implode(
            ', ',
            $missingNames,
        );

        $risk = $missing === ''
            ? 'prasyarat utama sudah cukup kuat'
            : 'perlu penguatan pada '.$missing;

        if ($userProject === null) {
            return "Kekuatan: proyek {$project->title} memiliki scope dan kriteria selesai yang jelas. Risiko: {$risk}. Langkah berikutnya: mulai dari satu fitur minimum, simpan bukti, lalu perbarui progres setelah alur tersebut benar-benar bekerja.";
        }

        return "Kekuatan: progres sudah tercatat {$userProject->progress_percentage}%. Risiko: {$risk}. Langkah berikutnya: gunakan catatan dan kriteria selesai untuk memilih satu bagian yang dapat dibuktikan pada sesi berikutnya, bukan menaikkan persentase tanpa bukti.";
    }
}
