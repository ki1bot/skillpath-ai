<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Cache;

class AiRateLimitStore
{
    public function blocked(
        string $provider,
        string $key,
    ): bool {
        return Cache::has(
            $this->cacheKey(
                $provider,
                $key,
            ),
        );
    }

    public function clear(
        string $provider,
        string $key,
    ): void {
        Cache::forget(
            $this->cacheKey(
                $provider,
                $key,
            ),
        );
    }

    public function shouldBlockProvider(
        string $provider,
        mixed $response,
    ): bool {
        if (
            $provider !== 'openrouter'
            || ! is_array($response)
        ) {
            return true;
        }

        return data_get(
            $response,
            'error.metadata.limit_source',
        ) !== 'upstream_provider_shared_pool';
    }

    public function remember(
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

                $ttlSeconds = max(
                    60,
                    min(
                        $resetTimestamp
                            - now()->getTimestamp(),
                        86400,
                    ),
                );
            }
        }

        Cache::put(
            $this->cacheKey(
                $provider,
                $key,
            ),
            true,
            now()->addSeconds($ttlSeconds),
        );
    }

    private function cacheKey(
        string $provider,
        string $key,
    ): string {
        return 'ai-rate-limit:'
            .$provider
            .':'
            .sha1($key);
    }
}
