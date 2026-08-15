<?php

namespace App\Services;

use App\Models\LearningMaterial;
use App\Models\PortfolioProject;
use App\Models\Roadmap;
use App\Models\RoadmapItem;
use App\Models\User;
use App\Models\UserProject;
use App\Support\AiCompletionResult;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
                12,
            ),
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
                12,
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
                12,
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
        int $timeoutSeconds = 15,
    ): ?array {
        $providers = [];

        $geminiKey = config(
            'services.gemini.key',
        );
        $geminiModel = config(
            'services.gemini.model',
            'gemini-3.5-flash-lite',
        );
        $geminiBaseUrl = config(
            'services.gemini.base_url',
            'https://generativelanguage.googleapis.com/v1beta',
        );
        $configuredGeminiFallbackModels = config(
            'services.gemini.fallback_models',
            [],
        );

        if (
            is_string($geminiKey)
            && trim($geminiKey) !== ''
            && is_string($geminiModel)
            && trim($geminiModel) !== ''
            && is_string($geminiBaseUrl)
            && trim($geminiBaseUrl) !== ''
        ) {
            $geminiModels = [
                trim($geminiModel),
            ];

            if (is_array($configuredGeminiFallbackModels)) {
                foreach ($configuredGeminiFallbackModels as $fallbackModel) {
                    if (
                        ! is_string($fallbackModel)
                        || trim($fallbackModel) === ''
                    ) {
                        continue;
                    }

                    $geminiModels[] = trim($fallbackModel);
                }
            }

            $providers[] = [
                'name' => 'gemini',
                'key' => trim($geminiKey),
                'base_url' => trim($geminiBaseUrl),
                'models' => array_values(
                    array_unique($geminiModels),
                ),
            ];
        }

        $openRouterKey = config(
            'services.openrouter.key',
        );
        $openRouterModel = config(
            'services.openrouter.model',
            'openai/gpt-oss-20b:free',
        );
        $openRouterBaseUrl = config(
            'services.openrouter.base_url',
            'https://openrouter.ai/api/v1',
        );
        $configuredFallbackModels = config(
            'services.openrouter.fallback_models',
            [],
        );

        if (
            is_string($openRouterKey)
            && trim($openRouterKey) !== ''
            && is_string($openRouterModel)
            && trim($openRouterModel) !== ''
            && is_string($openRouterBaseUrl)
            && trim($openRouterBaseUrl) !== ''
        ) {
            $openRouterModels = [
                trim($openRouterModel),
            ];

            if (is_array($configuredFallbackModels)) {
                foreach ($configuredFallbackModels as $fallbackModel) {
                    if (
                        ! is_string($fallbackModel)
                        || trim($fallbackModel) === ''
                    ) {
                        continue;
                    }

                    $openRouterModels[] = trim($fallbackModel);
                }
            }

            $providers[] = [
                'name' => 'openrouter',
                'key' => trim($openRouterKey),
                'base_url' => trim($openRouterBaseUrl),
                'models' => array_values(
                    array_unique($openRouterModels),
                ),
            ];
        }

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

        $providerSignature = '';

        foreach ($providers as $provider) {
            $providerSignature .= $provider['name']
                .'|'
                .$provider['base_url']
                .'|'
                .implode(',', $provider['models'])
                .';';
        }

        $cacheKey = 'skillpath-ai-insight:v10:'
            .$scope
            .':'
            .$user->id
            .':'
            .sha1(
                $providerSignature
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

        $failureCacheKey = $cacheKey.':failure';

        if (Cache::has($failureCacheKey)) {
            return null;
        }

        $startedAt = microtime(true);
        $deadline = $startedAt + max(
            3,
            $timeoutSeconds,
        );

        foreach ($providers as $provider) {
            if (
                Cache::has(
                    $this->rateLimitCacheKey(
                        $provider['name'],
                        $provider['key'],
                    ),
                )
            ) {
                continue;
            }

            foreach ($provider['models'] as $candidateModel) {
                $attemptTimeout = $this->nextAttemptTimeout(
                    $deadline,
                );

                if ($attemptTimeout === null) {
                    break 2;
                }

                $result = $this->requestInsight(
                    $provider['name'],
                    $provider['key'],
                    $provider['base_url'],
                    $candidateModel,
                    $task,
                    $json,
                    $maxTokens,
                    $attemptTimeout,
                    ...$requiredTags,
                );

                if ($result === false) {
                    break;
                }

                if ($result === null) {
                    continue;
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
        }

        Cache::put(
            $failureCacheKey,
            true,
            now()->addSeconds(
                (int) config(
                    'services.ai.failure_cache_seconds',
                    5,
                ),
            ),
        );

        Log::warning(
            'AI insight providers were exhausted.',
            [
                'scope' => $scope,
                'user_id' => $user->id,
                'elapsed_ms' => (int) round(
                    (microtime(true) - $startedAt) * 1000,
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

    private function requestInsight(
        string $provider,
        string $key,
        string $baseUrl,
        string $model,
        string $task,
        string $json,
        int $maxTokens,
        int $timeoutSeconds,
        string ...$requiredTags,
    ): AiCompletionResult|false|null {
        if ($provider === 'gemini') {
            return $this->requestGeminiInsight(
                $key,
                $baseUrl,
                $model,
                $task,
                $json,
                $maxTokens,
                $timeoutSeconds,
                ...$requiredTags,
            );
        }

        return $this->requestOpenRouterInsight(
            $key,
            $baseUrl,
            $model,
            $task,
            $json,
            $maxTokens,
            $timeoutSeconds,
            ...$requiredTags,
        );
    }

    private function requestGeminiInsight(
        string $key,
        string $baseUrl,
        string $model,
        string $task,
        string $json,
        int $maxTokens,
        int $timeoutSeconds,
        string ...$requiredTags,
    ): AiCompletionResult|false|null {
        try {
            $requiredTags = array_values(
                $requiredTags,
            );

            $generationConfig = $this->geminiGenerationConfig(
                $model,
                max(
                    $maxTokens,
                    1024,
                ),
            );

            $generationConfig['responseMimeType'] = 'application/json';
            $generationConfig['responseJsonSchema'] = $requiredTags !== []
                ? [
                    'type' => 'object',
                    'properties' => [
                        'progress' => [
                            'type' => 'string',
                        ],
                        'schedule' => [
                            'type' => 'string',
                        ],
                        'obstacles' => [
                            'type' => 'string',
                        ],
                    ],
                    'required' => [
                        'progress',
                        'schedule',
                        'obstacles',
                    ],
                    'additionalProperties' => false,
                ]
                : [
                    'type' => 'object',
                    'properties' => [
                        'content' => [
                            'type' => 'string',
                        ],
                    ],
                    'required' => [
                        'content',
                    ],
                    'additionalProperties' => false,
                ];

            $response = Http::withHeaders([
                'x-goog-api-key' => $key,
            ])
                ->acceptJson()
                ->asJson()
                ->connectTimeout(
                    min(
                        $timeoutSeconds,
                        (int) config(
                            'services.ai.connect_timeout',
                            3,
                        ),
                    ),
                )
                ->timeout($timeoutSeconds)
                ->post(
                    rtrim(
                        $baseUrl,
                        '/',
                    )
                        .'/models/'
                        .rawurlencode($model)
                        .':generateContent',
                    [
                        'systemInstruction' => [
                            'parts' => [
                                [
                                    'text' => $this->insightSystemPrompt(
                                        $task,
                                        ...$requiredTags,
                                    ),
                                ],
                            ],
                        ],
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    [
                                        'text' => $json,
                                    ],
                                ],
                            ],
                        ],
                        'generationConfig' => $generationConfig,
                    ],
                );

            if (! $response->successful()) {
                $rateLimited = $response->status() === 429;

                if ($rateLimited) {
                    $this->rememberRateLimit(
                        'gemini',
                        $key,
                        $response->json(),
                        $response->header('Retry-After'),
                    );
                }

                Log::warning(
                    'Gemini AI insight request failed.',
                    [
                        'status' => $response->status(),
                        'model' => $model,
                        'response' => Str::limit(
                            $response->body(),
                            500,
                            '',
                        ),
                    ],
                );

                return $rateLimited
                    ? false
                    : null;
            }

            $content = $this->extractGeminiText(
                $response->json(
                    'candidates.0.content.parts',
                ),
            );

            if (! is_string($content)) {
                Log::warning(
                    'Gemini AI insight response did not contain text.',
                    [
                        'model' => $model,
                        'finish_reason' => $response->json(
                            'candidates.0.finishReason',
                        ),
                    ],
                );

                return null;
            }

            $content = $this->prepareContent(
                $content,
                ...$requiredTags,
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
                Log::warning(
                    'Gemini AI insight response was rejected.',
                    [
                        'requested_model' => $model,
                        'model_version' => $response->json(
                            'modelVersion',
                        ),
                        'finish_reason' => $response->json(
                            'candidates.0.finishReason',
                        ),
                        'content' => Str::limit(
                            $content ?? '',
                            500,
                            '',
                        ),
                    ],
                );

                return null;
            }

            Cache::forget(
                $this->rateLimitCacheKey(
                    'gemini',
                    $key,
                ),
            );

            $modelVersion = $response->json(
                'modelVersion',
            );

            $resolvedModel = is_string($modelVersion)
                && trim($modelVersion) !== ''
                    ? trim($modelVersion)
                    : $model;

            return new AiCompletionResult(
                $content,
                $resolvedModel,
            );
        } catch (ConnectionException $exception) {
            Log::warning(
                'Gemini AI insight request timed out or could not connect.',
                [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        } catch (Throwable $exception) {
            Log::warning(
                'Gemini AI insight request threw an exception.',
                [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        }
    }

    private function requestOpenRouterInsight(
        string $key,
        string $baseUrl,
        string $model,
        string $task,
        string $json,
        int $maxTokens,
        int $timeoutSeconds,
        string ...$requiredTags,
    ): AiCompletionResult|false|null {
        try {
            $requiredTags = array_values(
                $requiredTags,
            );

            $payload = [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->insightSystemPrompt(
                            $task,
                            ...$requiredTags,
                        ),
                    ],
                    [
                        'role' => 'user',
                        'content' => $json,
                    ],
                ],
                'temperature' => 0.2,
                'max_tokens' => $maxTokens,
                'stream' => false,
                'provider' => [
                    'allow_fallbacks' => true,
                ],
            ];

            if ($this->shouldLimitOpenRouterReasoning($model)) {
                $payload['reasoning'] = [
                    'effort' => 'minimal',
                    'exclude' => true,
                ];
            }

            $response = Http::withToken(
                $key,
            )
                ->withHeaders([
                    'HTTP-Referer' => (string) config(
                        'app.url',
                    ),
                    'X-Title' => (string) config(
                        'app.name',
                    ),
                ])
                ->acceptJson()
                ->asJson()
                ->connectTimeout(
                    min(
                        $timeoutSeconds,
                        (int) config(
                            'services.ai.connect_timeout',
                            3,
                        ),
                    ),
                )
                ->timeout($timeoutSeconds)
                ->post(
                    rtrim(
                        $baseUrl,
                        '/',
                    ).'/chat/completions',
                    $payload,
                );

            if (! $response->successful()) {
                $rateLimited = $response->status() === 429;

                if ($rateLimited) {
                    $this->rememberRateLimit(
                        'openrouter',
                        $key,
                        $response->json(),
                        $response->header('Retry-After'),
                    );
                }

                Log::warning(
                    'OpenRouter AI insight request failed.',
                    [
                        'status' => $response->status(),
                        'model' => $model,
                        'response' => Str::limit(
                            $response->body(),
                            500,
                            '',
                        ),
                    ],
                );

                return $rateLimited
                    ? false
                    : null;
            }

            $content = $response->json(
                'choices.0.message.content',
            );

            if (! is_string($content)) {
                Log::warning(
                    'OpenRouter AI insight response did not contain text.',
                    [
                        'model' => $model,
                        'finish_reason' => $response->json(
                            'choices.0.finish_reason',
                        ),
                    ],
                );

                return null;
            }

            $content = $this->prepareContent(
                $content,
                ...$requiredTags,
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
                Log::warning(
                    'OpenRouter AI insight response was rejected.',
                    [
                        'requested_model' => $model,
                        'resolved_model' => $response->json(
                            'model',
                        ),
                        'finish_reason' => $response->json(
                            'choices.0.finish_reason',
                        ),
                        'content' => Str::limit(
                            (string) $response->json(
                                'choices.0.message.content',
                                '',
                            ),
                            500,
                            '',
                        ),
                    ],
                );

                return null;
            }

            Cache::forget(
                $this->rateLimitCacheKey(
                    'openrouter',
                    $key,
                ),
            );

            $responseModel = $response->json(
                'model',
            );

            $resolvedModel = is_string($responseModel)
                && trim($responseModel) !== ''
                    ? trim($responseModel)
                    : $model;

            return new AiCompletionResult(
                $content,
                $resolvedModel,
            );
        } catch (ConnectionException $exception) {
            Log::warning(
                'OpenRouter AI insight request timed out or could not connect.',
                [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        } catch (Throwable $exception) {
            Log::warning(
                'OpenRouter AI insight request threw an exception.',
                [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        }
    }

    private function insightSystemPrompt(
        string $task,
        string ...$requiredTags,
    ): string {
        return 'Anda adalah fitur AI SkillPath AI. Locale aplikasi saat ini adalah '.app()->getLocale().'. Seluruh teks yang ditampilkan kepada pengguna wajib menggunakan Bahasa Indonesia. Jangan menulis kalimat dalam Bahasa Inggris. Nama teknologi, framework, API, database, bahasa pemrograman, library, atau istilah teknis yang umum boleh tetap menggunakan nama aslinya. Gunakan hanya data yang diberikan. Jangan mengubah skor, hasil asesmen, status progres, keputusan roadmap, kemampuan, proyek, materi, atau fakta lain. Jangan membuat data yang tidak diberikan. Gunakan Bahasa Indonesia yang alami, jelas, dan ringkas. '.$task.' '.$this->outputInstruction(...$requiredTags);
    }

    private function extractGeminiText(
        mixed $parts,
    ): ?string {
        if (! is_array($parts)) {
            return null;
        }

        $texts = [];

        foreach ($parts as $part) {
            if (
                ! is_array($part)
                || ! is_string(
                    $part['text'] ?? null,
                )
                || trim($part['text']) === ''
            ) {
                continue;
            }

            $texts[] = trim($part['text']);
        }

        if ($texts === []) {
            return null;
        }

        return implode(
            "\n",
            $texts,
        );
    }

    private function outputInstruction(
        string ...$requiredTags,
    ): string {
        if ($requiredTags !== []) {
            return 'Kembalikan hanya JSON valid tanpa Markdown dengan tiga field string: progress, schedule, dan obstacles.';
        }

        return 'Kembalikan hanya JSON valid tanpa Markdown dengan satu field string: content.';
    }

    private function prepareContent(
        string $content,
        string ...$requiredTags,
    ): ?string {
        $content = $this->normalizeContent(
            $content,
        );

        if ($content === null) {
            return null;
        }

        $requiredTags = array_values(
            $requiredTags,
        );

        if (
            $requiredTags !== []
            && $this->hasRequiredTags(
                $content,
                $requiredTags,
            )
        ) {
            return $content;
        }

        $decoded = json_decode(
            $content,
            true,
        );

        if (! is_array($decoded)) {
            if ($requiredTags === []) {
                return $content;
            }

            return $this->parseProgressText($content);
        }

        if ($requiredTags === []) {
            $value = $decoded['content'] ?? null;

            return is_string($value)
                ? $this->normalizeContent($value)
                : null;
        }

        $progress = $decoded['progress'] ?? null;
        $schedule = $decoded['schedule'] ?? null;
        $obstacles = $decoded['obstacles'] ?? null;

        if (
            ! is_string($progress)
            || ! is_string($schedule)
            || ! is_string($obstacles)
        ) {
            return null;
        }

        $progress = $this->normalizeContent($progress);
        $schedule = $this->normalizeContent($schedule);
        $obstacles = $this->normalizeContent($obstacles);

        if (
            $progress === null
            || $schedule === null
            || $obstacles === null
        ) {
            return null;
        }

        return '<PROGRESS>'
            .$progress
            .'</PROGRESS>'
            .'<SCHEDULE>'
            .$schedule
            .'</SCHEDULE>'
            .'<OBSTACLES>'
            .$obstacles
            .'</OBSTACLES>';
    }

    private function parseProgressText(
        string $content,
    ): ?string {
        $patterns = [
            'progress' => '/(?:^|\n)\s*(?:progress|ringkasan perkembangan|perkembangan)\s*:?\s*(.*?)(?=\n\s*(?:schedule|saran jadwal belajar|jadwal)\s*:?|\z)/isu',
            'schedule' => '/(?:^|\n)\s*(?:schedule|saran jadwal belajar|jadwal)\s*:?\s*(.*?)(?=\n\s*(?:obstacles|pola kendala belajar|kendala)\s*:?|\z)/isu',
            'obstacles' => '/(?:^|\n)\s*(?:obstacles|pola kendala belajar|kendala)\s*:?\s*(.*?)\s*\z/isu',
        ];

        $result = [];

        foreach ($patterns as $key => $pattern) {
            if (
                preg_match(
                    $pattern,
                    $content,
                    $matches,
                ) !== 1
            ) {
                return null;
            }

            $value = $this->normalizeContent(
                (string) $matches[1],
            );

            if ($value === null) {
                return null;
            }

            $result[$key] = $value;
        }

        return '<PROGRESS>'
            .$result['progress']
            .'</PROGRESS>'
            .'<SCHEDULE>'
            .$result['schedule']
            .'</SCHEDULE>'
            .'<OBSTACLES>'
            .$result['obstacles']
            .'</OBSTACLES>';
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

        $content = preg_replace(
            '/^```(?:json|text)?\s*|\s*```$/iu',
            '',
            $content,
        );

        if (! is_string($content)) {
            return null;
        }

        $content = str_replace(
            [
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

    private function shouldLimitOpenRouterReasoning(
        string $model,
    ): bool {
        return str_contains(
            Str::lower($model),
            'gpt-oss',
        );
    }

    private function canDisableGeminiThinking(
        string $model,
    ): bool {
        $model = Str::lower($model);

        return str_contains(
            $model,
            'gemini-2.5-',
        )
            && ! str_contains(
                $model,
                'pro',
            );
    }

    /**
     * @return array<string, mixed>
     */
    private function geminiGenerationConfig(
        string $model,
        int $maxOutputTokens,
    ): array {
        $config = [
            'maxOutputTokens' => $maxOutputTokens,
        ];

        $thinkingLevel = $this->geminiThinkingLevel($model);

        if ($thinkingLevel !== null) {
            $config['thinkingConfig'] = [
                'thinkingLevel' => $thinkingLevel,
            ];

            return $config;
        }

        $config['temperature'] = 0.2;

        if ($this->canDisableGeminiThinking($model)) {
            $config['thinkingConfig'] = [
                'thinkingBudget' => 0,
            ];
        }

        return $config;
    }

    private function geminiThinkingLevel(
        string $model,
    ): ?string {
        $model = Str::lower(
            trim($model),
        );

        if (
            Str::contains(
                $model,
                [
                    'gemini-3.6-flash',
                    'gemini-3.5-flash',
                    'gemini-3.1-flash-lite',
                    'gemini-3-flash',
                    'gemini-flash-latest',
                ],
            )
        ) {
            return 'minimal';
        }

        if (
            preg_match(
                '/(?:^|\/)gemini-(?:[3-9]|\d{2,})(?:[.\-]|$)/i',
                $model,
            ) === 1
        ) {
            return 'low';
        }

        return null;
    }

    private function nextAttemptTimeout(
        float $deadline,
    ): ?int {
        $remainingSeconds = (int) floor(
            $deadline - microtime(true),
        );

        if ($remainingSeconds < 1) {
            return null;
        }

        return min(
            $remainingSeconds,
            (int) config(
                'services.ai.attempt_timeout',
                6,
            ),
        );
    }

    private function rememberRateLimit(
        string $provider,
        string $key,
        mixed $response,
        ?string $retryAfter,
    ): void {
        $ttlSeconds = 60;

        if (
            is_string($retryAfter)
            && is_numeric(trim($retryAfter))
        ) {
            $ttlSeconds = max(
                10,
                min(
                    (int) ceil(
                        (float) trim($retryAfter),
                    ),
                    3600,
                ),
            );
        }

        if (
            $provider === 'openrouter'
            && is_array($response)
            && data_get(
                $response,
                'error.metadata.limit_source',
            ) === 'openrouter_free_tier_daily'
        ) {
            $reset = data_get(
                $response,
                'error.metadata.headers.X-RateLimit-Reset',
            );

            if (is_numeric($reset)) {
                $resetTimestamp = (int) floor(
                    ((float) $reset) / 1000,
                );

                $currentTimestamp = now()->getTimestamp();

                $ttlSeconds = max(
                    60,
                    min(
                        $resetTimestamp - $currentTimestamp,
                        86400,
                    ),
                );
            }
        }

        Cache::put(
            $this->rateLimitCacheKey(
                $provider,
                $key,
            ),
            true,
            now()->addSeconds($ttlSeconds),
        );
    }

    private function rateLimitCacheKey(
        string $provider,
        string $key,
    ): string {
        return 'ai-rate-limit:'
            .$provider
            .':'
            .sha1($key);
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
