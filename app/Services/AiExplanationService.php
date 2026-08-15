<?php

namespace App\Services;

use App\Models\User;
use App\Support\AiExplanationResult;
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

        $key = config(
            'services.openrouter.key',
        );

        $model = config(
            'services.openrouter.model',
            'openai/gpt-oss-20b:free',
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
            return $unavailable;
        }

        $key = trim($key);
        $baseUrl = trim($baseUrl);

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

        $cacheKey = 'skill-gap-explanation:v8:'
            .$user->id
            .':'
            .sha1(
                $baseUrl
                    .'|'
                    .implode('|', $models)
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
        $rateLimitCacheKey = $this->rateLimitCacheKey($key);

        if (
            Cache::has($rateLimitCacheKey)
            || Cache::has($failureCacheKey)
        ) {
            return $unavailable;
        }

        foreach ($models as $candidateModel) {
            $result = $this->requestSummary(
                $key,
                $baseUrl,
                $candidateModel,
                $contextJson,
            );

            if ($result === false) {
                return $unavailable;
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
            now()->addSeconds(30),
        );

        return $unavailable;
    }

    private function requestSummary(
        string $key,
        string $baseUrl,
        string $model,
        string $contextJson,
    ): AiExplanationResult|false|null {
        try {
            $payload = [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Anda adalah fitur penjelasan SkillPath AI. Keputusan utama sudah dihitung oleh sistem berbasis data dan aturan. Tugas Anda hanya menjelaskan hasil tersebut dengan Bahasa Indonesia yang alami dan mudah dipahami. Gunakan hanya target karier, skor kemampuan, target, gap, priority score, status, dan prasyarat yang diberikan. Jangan membuat skill, nilai, kemampuan, fakta, roadmap, materi, proyek, atau hubungan prasyarat baru. Jangan mengubah urutan prioritas. Jangan memberi jaminan kesiapan kerja. Tulis tepat satu paragraf tanpa Markdown, tanpa JSON, tanpa judul, maksimal 120 kata. Utamakan kemampuan dengan gap dan priority score tertinggi serta jelaskan alasannya.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $contextJson,
                    ],
                ],
                'temperature' => 0.2,
                'max_tokens' => 500,
                'stream' => false,
                'provider' => [
                    'allow_fallbacks' => true,
                ],
            ];

            if ($this->shouldLimitReasoning($model)) {
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
                ->connectTimeout(4)
                ->timeout(18)
                ->post(
                    rtrim(
                        $baseUrl,
                        '/',
                    ).'/chat/completions',
                    $payload,
                );

            if (! $response->successful()) {
                $rateLimited = (
                    $response->status() === 429
                    && $this->isDailyFreeTierLimit(
                        $response->json(),
                    )
                );

                if ($rateLimited) {
                    $this->rememberRateLimit(
                        $key,
                        $response->json(),
                    );
                }

                Log::warning(
                    'OpenRouter skill gap request failed.',
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
                $response->json(
                    'choices.0.message.content',
                ),
            );

            if ($summary === null) {
                Log::warning(
                    'OpenRouter skill gap response was rejected.',
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
                $this->rateLimitCacheKey($key),
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
        } catch (Throwable $exception) {
            Log::warning(
                'OpenRouter skill gap request threw an exception.',
                [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        }
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

    private function shouldLimitReasoning(
        string $model,
    ): bool {
        return str_contains(
            Str::lower($model),
            'gpt-oss',
        );
    }

    private function isDailyFreeTierLimit(
        mixed $response,
    ): bool {
        return is_array($response)
            && data_get(
                $response,
                'error.metadata.limit_source',
            ) === 'openrouter_free_tier_daily';
    }

    private function rememberRateLimit(
        string $key,
        mixed $response,
    ): void {
        $ttlSeconds = 900;

        if (is_array($response)) {
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
            $this->rateLimitCacheKey($key),
            true,
            now()->addSeconds($ttlSeconds),
        );
    }

    private function rateLimitCacheKey(
        string $key,
    ): string {
        return 'openrouter-rate-limit:'
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
