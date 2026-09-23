<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class AiProviderHealth
{
    /**
     * @param  list<array{
     *     name: string,
     *     key: string,
     *     base_url: string,
     *     models: list<string>
     * }>  $providers
     * @return list<array{
     *     provider: array{
     *         name: string,
     *         key: string,
     *         base_url: string,
     *         models: list<string>
     *     },
     *     model: string
     * }>
     */
    public function orderedAttempts(
        array $providers,
    ): array {
        $priorityMap = $this->providerPriorityMap();
        $now = time();
        $attempts = [];

        foreach (
            $providers as $providerIndex => $provider
        ) {
            $providerPriority = $priorityMap[
                $provider['name']
            ] ?? (
                count($priorityMap)
                + $providerIndex
            );

            foreach (
                $provider['models'] as $modelIndex => $model
            ) {
                $state = $this->state(
                    $provider['name'],
                    $model,
                    $provider['key'],
                );

                $latencyMs = max(
                    0,
                    (int) (
                        $state['latency_ms']
                        ?? 0
                    ),
                );

                $attempts[] = [
                    'provider' => $provider,
                    'model' => $model,
                    'score' => (
                        $modelIndex * 10000
                    ) + (
                        $providerPriority * 1000
                    ) + min(
                        900,
                        intdiv(
                            $latencyMs,
                            20,
                        ),
                    ),
                    'cooldown_until' => max(
                        0,
                        (int) (
                            $state['cooldown_until']
                            ?? 0
                        ),
                    ),
                ];
            }
        }

        if ($attempts === []) {
            return [];
        }

        $ready = array_values(
            array_filter(
                $attempts,
                fn (array $attempt): bool => (
                    $attempt['cooldown_until']
                    <= $now
                ),
            ),
        );

        if ($ready === []) {
            usort(
                $attempts,
                function (
                    array $left,
                    array $right,
                ): int {
                    $cooldownComparison = (
                        $left['cooldown_until']
                        <=> $right['cooldown_until']
                    );

                    if ($cooldownComparison !== 0) {
                        return $cooldownComparison;
                    }

                    return $left['score']
                        <=> $right['score'];
                },
            );

            $ready = [
                $attempts[0],
            ];
        } else {
            usort(
                $ready,
                fn (
                    array $left,
                    array $right,
                ): int => (
                    $left['score']
                    <=> $right['score']
                ),
            );
        }

        return array_map(
            fn (array $attempt): array => [
                'provider' => $attempt[
                    'provider'
                ],
                'model' => $attempt[
                    'model'
                ],
            ],
            $ready,
        );
    }

    public function recordFailure(
        string $provider,
        string $model,
        string $key,
        bool $providerBlocked = false,
    ): void {
        $state = $this->state(
            $provider,
            $model,
            $key,
        );

        $failures = min(
            6,
            max(
                0,
                (int) (
                    $state['failures']
                    ?? 0
                ),
            ) + 1,
        );

        $baseCooldown = max(
            10,
            (int) config(
                'services.ai.health_cooldown_seconds',
                45,
            ),
        );

        $maxCooldown = max(
            $baseCooldown,
            (int) config(
                'services.ai.health_max_cooldown_seconds',
                300,
            ),
        );

        $cooldownSeconds = min(
            $maxCooldown,
            (int) (
                $baseCooldown
                * (2 ** ($failures - 1))
            ),
        );

        if ($providerBlocked) {
            $cooldownSeconds = max(
                $cooldownSeconds,
                min(
                    $maxCooldown,
                    120,
                ),
            );
        }

        $stateSeconds = max(
            $cooldownSeconds + 60,
            (int) config(
                'services.ai.health_state_seconds',
                600,
            ),
        );

        Cache::put(
            $this->cacheKey(
                $provider,
                $model,
                $key,
            ),
            [
                'failures' => $failures,
                'cooldown_until' => time()
                    + $cooldownSeconds,
                'latency_ms' => max(
                    0,
                    (int) (
                        $state['latency_ms']
                        ?? 0
                    ),
                ),
                'last_success_at' => max(
                    0,
                    (int) (
                        $state['last_success_at']
                        ?? 0
                    ),
                ),
            ],
            now()->addSeconds(
                $stateSeconds,
            ),
        );
    }

    public function recordSuccess(
        string $provider,
        string $model,
        string $key,
        int $latencyMs,
    ): void {
        Cache::put(
            $this->cacheKey(
                $provider,
                $model,
                $key,
            ),
            [
                'failures' => 0,
                'cooldown_until' => 0,
                'latency_ms' => max(
                    0,
                    $latencyMs,
                ),
                'last_success_at' => time(),
            ],
            now()->addSeconds(
                max(
                    60,
                    (int) config(
                        'services.ai.health_state_seconds',
                        600,
                    ),
                ),
            ),
        );
    }

    /**
     * @return array{
     *     failures: int,
     *     cooldown_until: int,
     *     latency_ms: int,
     *     last_success_at: int
     * }|array{}
     */
    private function state(
        string $provider,
        string $model,
        string $key,
    ): array {
        $state = Cache::get(
            $this->cacheKey(
                $provider,
                $model,
                $key,
            ),
        );

        if (! is_array($state)) {
            return [];
        }

        return [
            'failures' => max(
                0,
                (int) (
                    $state['failures']
                    ?? 0
                ),
            ),
            'cooldown_until' => max(
                0,
                (int) (
                    $state['cooldown_until']
                    ?? 0
                ),
            ),
            'latency_ms' => max(
                0,
                (int) (
                    $state['latency_ms']
                    ?? 0
                ),
            ),
            'last_success_at' => max(
                0,
                (int) (
                    $state['last_success_at']
                    ?? 0
                ),
            ),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function providerPriorityMap(): array
    {
        $order = config(
            'services.ai.provider_order',
            [
                'openrouter',
                'xkiro',
                'gemini',
                'tokenrouter',
            ],
        );

        if (is_string($order)) {
            $order = explode(
                ',',
                $order,
            );
        }

        if (! is_array($order)) {
            $order = [];
        }

        $priority = [];

        foreach ($order as $index => $provider) {
            if (
                ! is_string($provider)
                || trim($provider) === ''
            ) {
                continue;
            }

            $priority[
                strtolower(
                    trim($provider),
                )
            ] = (int) $index;
        }

        return $priority;
    }

    private function cacheKey(
        string $provider,
        string $model,
        string $key,
    ): string {
        return 'skillpath-ai-provider-health:v1:'
            .sha1(
                strtolower(
                    trim($provider),
                )
                .'|'
                .trim($model)
                .'|'
                .hash(
                    'sha256',
                    $key,
                ),
            );
    }
}
