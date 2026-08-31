<?php

namespace App\Services;

use App\Models\User;
use App\Support\AiExplanationResult;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AiExplanationService
{
    public function skillGapSummary(
        User $user,
        array $analysis,
    ): AiExplanationResult {
        $unavailable = new AiExplanationResult(
            null,
            false,
        );

        $skills = collect(
            $analysis,
        )
            ->take(8)
            ->map(
                fn (array $item) => [
                    'skill' => $item['name'],
                    'current' => $item['current'],
                    'target' => $item['target'],
                    'gap' => $item['gap'],
                    'priority' => $item['priority'],
                    'status' => $item['status'],
                    'prerequisites' => collect(
                        $item['prerequisites'],
                    )
                        ->pluck('name')
                        ->all(),
                ],
            )
            ->values()
            ->all();

        if ($skills === []) {
            return $unavailable;
        }

        $careerName = $user
            ->targetCareer
            ?->name;

        if (
            ! is_string($careerName)
            || trim($careerName) === ''
        ) {
            return $unavailable;
        }

        $careerName = trim($careerName);
        $providers = $this->configuredProviders();

        if ($providers === []) {
            return $unavailable;
        }

        $context = [
            'target_career' => $careerName,
            'skills' => $skills,
        ];

        $contextJson = json_encode(
            $context,
            JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES,
        );

        if (! is_string($contextJson)) {
            return $unavailable;
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

        $cacheKey = 'skill-gap-explanation:v13:'
            .$user->id
            .':'
            .sha1(
                $providerSignature
                    .'|'
                    .$contextJson,
            );

        $cached = Cache::get(
            $cacheKey,
        );

        if (
            is_array($cached)
            && ($cached['generated_by_ai'] ?? false) === true
            && is_string(
                $cached['summary'] ?? null,
            )
            && trim($cached['summary']) !== ''
            && $this->looksIndonesian(
                $cached['summary'],
            )
        ) {
            $cachedModel = $cached['model'] ?? null;

            return new AiExplanationResult(
                trim($cached['summary']),
                true,
                is_string($cachedModel)
                    && trim($cachedModel) !== ''
                        ? trim($cachedModel)
                        : null,
            );
        }

        $failureCacheKey = $cacheKey.':failure';

        if (Cache::has($failureCacheKey)) {
            return $unavailable;
        }

        $startedAt = microtime(true);
        $deadline = $startedAt + (float) config(
            'services.ai.request_timeout',
            20,
        );

        $blockedProviders = [];

        foreach ($this->orderedAttempts($providers) as $attempt) {
            $provider = $attempt['provider'];

            if (
                isset(
                    $blockedProviders[$provider['name']],
                )
            ) {
                continue;
            }

            if (
                Cache::has(
                    $this->rateLimitCacheKey(
                        $provider['name'],
                        $provider['key'],
                    ),
                )
            ) {
                $blockedProviders[$provider['name']] = true;

                continue;
            }

            $attemptTimeout = $this->nextAttemptTimeout(
                $deadline,
            );

            if ($attemptTimeout === null) {
                break;
            }

            $result = $this->requestSummary(
                $provider['name'],
                $provider['key'],
                $provider['base_url'],
                $attempt['model'],
                $contextJson,
                $attemptTimeout,
            );

            if ($result === false) {
                $blockedProviders[$provider['name']] = true;

                continue;
            }

            if ($result === null) {
                continue;
            }

            Cache::forget($failureCacheKey);

            Cache::put(
                $cacheKey,
                [
                    'summary' => $result->summary,
                    'model' => $result->model,
                    'generated_by_ai' => true,
                ],
                now()->addDays(7),
            );

            return $result;
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
            'AI skill gap providers were exhausted.',
            [
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

        return $unavailable;
    }

    private function configuredProviders(): array
    {
        $definitions = [
            [
                'name' => 'gemini',
                'key' => config('services.gemini.key'),
                'model' => config(
                    'services.gemini.model',
                    'gemini-3.5-flash-lite',
                ),
                'fallback_models' => config(
                    'services.gemini.fallback_models',
                    [],
                ),
                'base_url' => config(
                    'services.gemini.base_url',
                    'https://generativelanguage.googleapis.com/v1beta',
                ),
            ],
            [
                'name' => 'openrouter',
                'key' => config('services.openrouter.key'),
                'model' => config(
                    'services.openrouter.model',
                    'minimax/minimax-m3:free',
                ),
                'fallback_models' => config(
                    'services.openrouter.fallback_models',
                    [],
                ),
                'base_url' => config(
                    'services.openrouter.base_url',
                    'https://openrouter.ai/api/v1',
                ),
            ],
            [
                'name' => 'tokenrouter',
                'key' => config('services.tokenrouter.key'),
                'model' => config(
                    'services.tokenrouter.model',
                    'z-ai/glm-5.3-free',
                ),
                'fallback_models' => config(
                    'services.tokenrouter.fallback_models',
                    [],
                ),
                'base_url' => config(
                    'services.tokenrouter.base_url',
                    'https://api.tokenrouter.com/v1',
                ),
            ],
            [
                'name' => 'xkiro',
                'key' => config('services.xkiro.key'),
                'model' => config(
                    'services.xkiro.model',
                    'deepseek/deepseek-v4-pro',
                ),
                'fallback_models' => config(
                    'services.xkiro.fallback_models',
                    [],
                ),
                'base_url' => config(
                    'services.xkiro.base_url',
                    'https://api.xkiro.com/v1',
                ),
            ],
        ];

        $providers = [];

        foreach ($definitions as $definition) {
            if (
                ! is_string($definition['key'])
                || trim($definition['key']) === ''
                || ! is_string($definition['model'])
                || trim($definition['model']) === ''
                || ! is_string($definition['base_url'])
                || trim($definition['base_url']) === ''
            ) {
                continue;
            }

            $models = [
                trim($definition['model']),
            ];

            if (is_array($definition['fallback_models'])) {
                foreach (
                    $definition['fallback_models']
                    as $fallbackModel
                ) {
                    if (
                        ! is_string($fallbackModel)
                        || trim($fallbackModel) === ''
                    ) {
                        continue;
                    }

                    $models[] = trim($fallbackModel);
                }
            }

            $providers[] = [
                'name' => $definition['name'],
                'key' => trim($definition['key']),
                'base_url' => trim($definition['base_url']),
                'models' => array_values(
                    array_unique($models),
                ),
            ];
        }

        return $providers;
    }

    private function orderedAttempts(
        array $providers,
    ): array {
        $attempts = [];
        $maximumModels = 0;

        foreach ($providers as $provider) {
            $maximumModels = max(
                $maximumModels,
                count($provider['models']),
            );
        }

        for (
            $modelIndex = 0;
            $modelIndex < $maximumModels;
            $modelIndex++
        ) {
            foreach ($providers as $provider) {
                if (
                    ! isset(
                        $provider['models'][$modelIndex],
                    )
                ) {
                    continue;
                }

                $attempts[] = [
                    'provider' => $provider,
                    'model' => $provider['models'][$modelIndex],
                ];
            }
        }

        return $attempts;
    }

    private function requestSummary(
        string $provider,
        string $key,
        string $baseUrl,
        string $model,
        string $contextJson,
        int $timeoutSeconds,
    ): AiExplanationResult|false|null {
        if ($provider === 'gemini') {
            return $this->requestGeminiSummary(
                $key,
                $baseUrl,
                $model,
                $contextJson,
                $timeoutSeconds,
            );
        }

        return $this->requestOpenAiCompatibleSummary(
            $provider,
            $key,
            $baseUrl,
            $model,
            $contextJson,
            $timeoutSeconds,
        );
    }

    private function requestGeminiSummary(
        string $key,
        string $baseUrl,
        string $model,
        string $contextJson,
        int $timeoutSeconds,
    ): AiExplanationResult|false|null {
        try {
            $generationConfig = $this->geminiGenerationConfig(
                $model,
                1024,
            );

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
                                    'text' => $this->summarySystemPrompt(),
                                ],
                            ],
                        ],
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    [
                                        'text' => $contextJson,
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
                    'Gemini skill gap request failed.',
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

            $summary = $this->normalizeSummary(
                $this->extractGeminiText(
                    $response->json(
                        'candidates.0.content.parts',
                    ),
                ),
            );

            if ($summary === null) {
                Log::warning(
                    'Gemini skill gap response was rejected.',
                    [
                        'requested_model' => $model,
                        'model_version' => $response->json(
                            'modelVersion',
                        ),
                        'finish_reason' => $response->json(
                            'candidates.0.finishReason',
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

            return new AiExplanationResult(
                $summary,
                true,
                $resolvedModel,
            );
        } catch (ConnectionException $exception) {
            Log::warning(
                'Gemini skill gap request timed out or could not connect.',
                [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        } catch (Throwable $exception) {
            Log::warning(
                'Gemini skill gap request threw an exception.',
                [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        }
    }

    private function requestOpenAiCompatibleSummary(
        string $provider,
        string $key,
        string $baseUrl,
        string $model,
        string $contextJson,
        int $timeoutSeconds,
    ): AiExplanationResult|false|null {
        try {
            $payload = [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->summarySystemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $contextJson,
                    ],
                ],
                'temperature' => 0.2,
                'max_tokens' => 500,
                'stream' => false,
            ];

            if ($provider === 'openrouter') {
                $payload['provider'] = [
                    'allow_fallbacks' => true,
                ];

                if (
                    $this->shouldLimitOpenRouterReasoning(
                        $model,
                    )
                ) {
                    $payload['reasoning'] = [
                        'effort' => 'minimal',
                        'exclude' => true,
                    ];
                }
            }

            $request = Http::withToken(
                $key,
            );

            if ($provider === 'openrouter') {
                $request->withHeaders([
                    'HTTP-Referer' => (string) config(
                        'app.url',
                    ),
                    'X-Title' => (string) config(
                        'app.name',
                    ),
                ]);
            }

            $response = $request
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
                        $provider,
                        $key,
                        $response->json(),
                        $response->header('Retry-After'),
                    );
                }

                Log::warning(
                    $this->providerLabel($provider)
                        .' skill gap request failed.',
                    [
                        'provider' => $provider,
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

            $summary = $this->normalizeSummary(
                $response->json(
                    'choices.0.message.content',
                ),
            );

            if ($summary === null) {
                Log::warning(
                    $this->providerLabel($provider)
                        .' skill gap response was rejected.',
                    [
                        'provider' => $provider,
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
                    $provider,
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

            return new AiExplanationResult(
                $summary,
                true,
                $resolvedModel,
            );
        } catch (ConnectionException $exception) {
            Log::warning(
                $this->providerLabel($provider)
                    .' skill gap request timed out or could not connect.',
                [
                    'provider' => $provider,
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        } catch (Throwable $exception) {
            Log::warning(
                $this->providerLabel($provider)
                    .' skill gap request threw an exception.',
                [
                    'provider' => $provider,
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        }
    }

    private function providerLabel(
        string $provider,
    ): string {
        return match ($provider) {
            'openrouter' => 'OpenRouter',
            'tokenrouter' => 'TokenRouter',
            'xkiro' => 'xKiro',
            default => Str::headline($provider),
        };
    }

    private function summarySystemPrompt(): string
    {
        return 'Anda adalah fitur penjelasan SkillPath AI. Keputusan utama sudah dihitung oleh sistem berbasis data dan aturan. Tugas Anda hanya menjelaskan hasil tersebut dengan Bahasa Indonesia yang alami dan mudah dipahami. Gunakan hanya target karier, skor kemampuan, target, gap, priority score, status, dan prasyarat yang diberikan. Jangan membuat skill, nilai, kemampuan, fakta, roadmap, materi, proyek, atau hubungan prasyarat baru. Jangan mengubah urutan prioritas. Jangan memberi jaminan kesiapan kerja. Tulis tepat satu paragraf tanpa Markdown, tanpa JSON, tanpa judul, maksimal 120 kata. Utamakan kemampuan dengan gap dan priority score tertinggi serta jelaskan alasannya.';
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

    private function normalizeSummary(
        mixed $content,
    ): ?string {
        if (! is_string($content)) {
            return null;
        }

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

        $decoded = json_decode(
            $content,
            true,
        );

        if (
            is_array($decoded)
            && is_string(
                $decoded['summary'] ?? null,
            )
        ) {
            $content = $decoded['summary'];
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
            '/^(?:ringkasan|summary)\s*:\s*/iu',
            '',
            $content,
        );

        if (! is_string($content)) {
            return null;
        }

        $content = preg_replace(
            '/\s+/u',
            ' ',
            $content,
        );

        if (! is_string($content)) {
            return null;
        }

        $content = trim(
            $content,
        );

        if (
            $content === ''
            || ! $this->looksIndonesian(
                $content,
            )
        ) {
            return null;
        }

        return Str::limit(
            $content,
            700,
            '',
        );
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
                4,
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
            '/\b(?:yang|dan|untuk|dengan|dari|pada|adalah|karena|anda|kamu|kemampuan|belajar|prioritas|utama|saat|masih|selisih|setelah|perlu|berikutnya|kesiapan|proyek|materi|kendala|waktu|target|nilai|penguatan|risiko|langkah|evaluasi|jadwal|progres|perkembangan|hasil|pengguna|tercatat|memiliki|menjadi|berada|dapat|belum|lebih|sesuai|kesenjangan|karier|diprioritaskan|memenuhi|fondasi|lanjutan|penguasaan|dasar|terhadap|sehingga|terutama|berfokus|meningkatkan|menuju|skor|urutan|prasyarat|keterampilan|berdasarkan|dibutuhkan|dipelajari)\b/u',
            $text,
            $indonesianMatches,
        );

        $englishCount = preg_match_all(
            '/\b(?:the|and|for|with|from|this|that|your|you|is|are|was|were|to|of|in|on|because|after|before|current|learning|should|needs|need|next|improve|improved|based|using|use|has|have|still|result|results|successfully|created|development|strength|risk|step|steps|weekly|minutes|recorded)\b/u',
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
}
