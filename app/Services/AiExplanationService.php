<?php

namespace App\Services;

use App\Models\User;
use App\Services\Ai\AiCompletionCoordinator;
use App\Services\Ai\AiExplanationFormatter;
use App\Services\Ai\AiProviderRegistry;
use App\Support\AiExplanationResult;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AiExplanationService
{
    public function __construct(
        private readonly AiProviderRegistry $providers,
        private readonly AiCompletionCoordinator $coordinator,
        private readonly AiExplanationFormatter $formatter,
    ) {}

    public function skillGapSummary(
        User $user,
        array $analysis,
    ): AiExplanationResult {
        $unavailable = new AiExplanationResult(
            null,
            false,
        );

        $skills = collect($analysis)
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

        $providers = $this->providers->configured();

        if ($providers === []) {
            return $unavailable;
        }

        $contextJson = json_encode(
            [
                'target_career' => trim($careerName),
                'skills' => $skills,
            ],
            JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES,
        );

        if (! is_string($contextJson)) {
            return $unavailable;
        }

        $cacheKey = 'skill-gap-explanation:v13:'
            .$user->id
            .':'
            .sha1(
                $this->providers->signature(
                    $providers,
                )
                    .'|'
                    .$contextJson,
            );

        $cached = Cache::get($cacheKey);

        if (
            is_array($cached)
            && ($cached['generated_by_ai'] ?? false) === true
            && is_string(
                $cached['summary'] ?? null,
            )
            && $this->formatter
                ->validCachedSummary(
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

        $result = $this->coordinator->complete(
            $providers,
            $this->formatter->systemPrompt(),
            $contextJson,
            500,
            (int) config(
                'services.ai.request_timeout',
                30,
            ),
            fn (string $content): ?string => $this
                ->formatter
                ->normalizeSummary($content),
            null,
            'skill gap',
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
                'AI skill gap providers were exhausted.',
                [
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

            return $unavailable;
        }

        Cache::forget($failureCacheKey);

        Cache::put(
            $cacheKey,
            [
                'summary' => $result->content,
                'model' => $result->model,
                'generated_by_ai' => true,
            ],
            now()->addDays(7),
        );

        return new AiExplanationResult(
            $result->content,
            true,
            $result->model,
        );
    }
}
