<?php

namespace App\Services;

use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Models\User;
use App\Models\UserProject;
use App\Services\Ai\AiCompletionCoordinator;
use App\Services\Ai\AiInsightFormatter;
use App\Services\Ai\AiProviderRegistry;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AiInsightService
{
    public function __construct(
        private readonly AiProviderRegistry $providers,
        private readonly AiCompletionCoordinator $coordinator,
        private readonly AiInsightFormatter $formatter,
    ) {}

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
            'Rangkum perkembangan kesiapan berdasarkan data pada progress. Berikan saran pembagian waktu belajar berdasarkan waktu belajar dan materi yang tersedia pada schedule. Jelaskan pola kendala berdasarkan kendala yang benar-benar tercatat pada obstacles. Jangan membuat nilai, skill, materi, progres, kendala, atau fakta baru. Setiap bagian maksimal 90 kata.',
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
            900,
            [
                'PROGRESS',
                'SCHEDULE',
                'OBSTACLES',
            ],
            (int) config(
                'services.ai.request_timeout',
                30,
            ),
        );

        if ($result === null) {
            return $this->emptyProgress();
        }

        $sections = $this->formatter->sections(
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
            'Berikan umpan balik proyek yang memiliki tiga bagian teks: Kekuatan, Risiko, dan Langkah berikutnya. Gunakan hanya deskripsi proyek, kesiapan, progres, dan catatan pengguna yang diberikan. Jangan mengklaim membaca source code atau repository. Jangan membuat progres, fakta, atau kemampuan baru. Maksimal 140 kata.',
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
            600,
            [],
            (int) config(
                'services.ai.request_timeout',
                30,
            ),
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
            500,
            [],
            (int) config(
                'services.ai.request_timeout',
                30,
            ),
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
        int $timeoutSeconds = 30,
    ): ?array {
        $providers = $this->providers->configured();

        if ($providers === []) {
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

        $cacheKey = 'skillpath-ai-insight:v11:'
            .$scope
            .':'
            .$user->id
            .':'
            .sha1(
                $this->providers->signature(
                    $providers,
                )
                    .'|'
                    .$json,
            );

        $cached = Cache::get($cacheKey);

        if (
            is_array($cached)
            && is_string(
                $cached['content'] ?? null,
            )
            && is_string(
                $cached['model'] ?? null,
            )
            && $this->formatter
                ->validCachedContent(
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

        $failureCacheKey = $cacheKey.':failure';

        if (Cache::has($failureCacheKey)) {
            return null;
        }

        $startedAt = microtime(true);

        $result = $this->coordinator->complete(
            $providers,
            $this->formatter->systemPrompt(
                $task,
                $requiredTags,
            ),
            $json,
            $maxTokens,
            $timeoutSeconds,
            fn (string $content): ?string => $this
                ->formatter
                ->normalize(
                    $content,
                    $requiredTags,
                ),
            $this->formatter->geminiJsonSchema(
                $requiredTags,
            ),
            'AI insight',
        );

        if ($result === null) {
            Cache::put(
                $failureCacheKey,
                true,
                now()->addSeconds(
                    (int) config(
                        'services.ai.failure_cache_seconds',
                        10,
                    ),
                ),
            );

            Log::warning(
                'AI insight providers were exhausted.',
                [
                    'scope' => $scope,
                    'user_id' => $user->id,
                    'elapsed_ms' => (int) round(
                        (
                            microtime(true)
                            - $startedAt
                        ) * 1000,
                    ),
                    'providers' => collect($providers)
                        ->map(
                            fn (array $provider) => [
                                'name' => $provider['name'],
                                'models' => $provider['models'],
                            ],
                        )
                        ->values()
                        ->all(),
                ],
            );

            return null;
        }

        Cache::forget($failureCacheKey);

        Cache::put(
            $cacheKey,
            [
                'content' => $result->content,
                'model' => $result->model,
            ],
            now()->addDays(7),
        );

        return [
            'content' => $result->content,
            'generated_by_ai' => true,
            'model' => $result->model,
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
