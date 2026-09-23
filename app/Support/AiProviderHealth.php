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
        $tieBreakMap = $this->providerTieBreakMap();
        $now = now()->getTimestamp();
        $attempts = [];

        foreach (
            $providers as $providerIndex => $provider
        ) {
            $providerTieBreak = $tieBreakMap[
                $provider['name']
            ] ?? (
                count($tieBreakMap)
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

                $cooldownUntil = max(
                    0,
                    (int) (
                        $state['cooldown_until']
                        ?? 0
                    ),
                );

                $failures = max(
                    0,
                    (int) (
                        $state['failures']
                        ?? 0
                    ),
                );

                $isProbe = $failures > 0
                    && $cooldownUntil <= $now;

                $attempts[] = [
                    'provider' => $provider,
                    'model' => $model,
                    'score' => $this->attemptScore(
                        $state,
                        $providerTieBreak,
                        $modelIndex,
                        $isProbe,
                    ),
                    'cooldown_until' => $cooldownUntil,
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

                'cooldown_until' => now()
                    ->addSeconds(
                        $cooldownSeconds,
                    )
                    ->getTimestamp(),

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

                'last_failure_at' => now()
                    ->getTimestamp(),
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
        $state = $this->state(
            $provider,
            $model,
            $key,
        );

        $latencyMs = max(
            1,
            $latencyMs,
        );

        $previousLatency = max(
            0,
            (int) (
                $state['latency_ms']
                ?? 0
            ),
        );

        $smoothedLatency = $previousLatency > 0
            ? (int) round(
                ($previousLatency * 0.7)
                + ($latencyMs * 0.3),
            )
            : $latencyMs;

        Cache::put(
            $this->cacheKey(
                $provider,
                $model,
                $key,
            ),
            [
                'failures' => 0,

                'cooldown_until' => 0,

                'latency_ms' => $smoothedLatency,

                'last_success_at' => now()
                    ->getTimestamp(),

                'last_failure_at' => max(
                    0,
                    (int) (
                        $state['last_failure_at']
                        ?? 0
                    ),
                ),
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
     * @param  array{
     *     failures?: int,
     *     cooldown_until?: int,
     *     latency_ms?: int,
     *     last_success_at?: int,
     *     last_failure_at?: int
     * }  $state
     */
    private function attemptScore(
        array $state,
        int $providerTieBreak,
        int $modelIndex,
        bool $isProbe,
    ): int {
        /*
         * Primary model tetap didahulukan daripada fallback.
         *
         * Model index:
         * 0 = primary
         * 1 = fallback pertama
         * 2 = fallback berikutnya
         */
        $tierBase = $modelIndex * 1_000_000;

        /*
         * Setelah cooldown selesai, provider yang pernah gagal
         * diberikan kesempatan satu kali untuk diuji ulang.
         *
         * Ini adalah mekanisme half-open circuit breaker.
         */
        if ($isProbe) {
            return $tierBase
                - 100_000
                + $providerTieBreak;
        }

        $latencyMs = max(
            0,
            (int) (
                $state['latency_ms']
                ?? 0
            ),
        );

        /*
         * Provider yang belum pernah diukur diberi nilai netral
         * 8 detik.
         *
         * Begitu latency nyata tersedia, latency tersebut menjadi
         * faktor utama pemilihan provider.
         */
        $effectiveLatency = $latencyMs > 0
            ? min(
                $latencyMs,
                60_000,
            )
            : 8_000;

        /*
         * providerTieBreak hanya digunakan untuk kondisi
         * latency sama / cold start.
         *
         * Jadi AI_PROVIDER_ORDER tidak lagi memaksa urutan provider.
         */
        return $tierBase
            + $effectiveLatency
            + ($providerTieBreak * 10);
    }

    /**
     * @return array{
     *     failures: int,
     *     cooldown_until: int,
     *     latency_ms: int,
     *     last_success_at: int,
     *     last_failure_at: int
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

            'last_failure_at' => max(
                0,
                (int) (
                    $state['last_failure_at']
                    ?? 0
                ),
            ),
        ];
    }

    /**
     * Dipakai hanya sebagai tie-breaker ketika belum ada
     * data latency provider.
     *
     * @return array<string, int>
     */
    private function providerTieBreakMap(): array
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
        /*
         * v2 sengaja dipakai agar cache dari algoritma lama
         * tidak ikut digunakan.
         */
        return 'skillpath-ai-provider-health:v2:'
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
