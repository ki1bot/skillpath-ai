<?php

namespace App\Services\Ai;

use App\Support\AiCompletionResult;
use App\Support\AiProviderHealth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiCompletionCoordinator
{
    public function __construct(
        private readonly AiProviderHealth $providerHealth,
        private readonly AiProviderClient $client,
        private readonly AiRateLimitStore $rateLimits,
        private readonly AiProviderRegistry $registry,
    ) {}

    /**
     * @param  list<array{
     *     name: string,
     *     key: string,
     *     base_url: string,
     *     models: list<string>
     * }>  $providers
     * @param  callable(string): ?string  $normalize
     * @param  array<string, mixed>|null  $geminiJsonSchema
     */
    public function complete(
        array $providers,
        string $systemPrompt,
        string $userPrompt,
        int $maxTokens,
        int $requestTimeout,
        callable $normalize,
        ?array $geminiJsonSchema = null,
        string $logScope = 'AI',
    ): ?AiCompletionResult {
        if ($providers === []) {
            return null;
        }

        $deadline = microtime(true) + max(
            3,
            $requestTimeout,
        );

        $blockedProviders = [];

        foreach (
            $this->providerHealth
                ->orderedAttempts($providers) as $attempt
        ) {
            $provider = $attempt['provider'];
            $model = $attempt['model'];

            if (
                isset(
                    $blockedProviders[
                        $provider['name']
                    ],
                )
            ) {
                continue;
            }

            if (
                $this->rateLimits->blocked(
                    $provider['name'],
                    $provider['key'],
                )
            ) {
                $blockedProviders[
                    $provider['name']
                ] = true;

                continue;
            }

            $attemptTimeout = $this
                ->nextAttemptTimeout($deadline);

            if ($attemptTimeout === null) {
                break;
            }

            $attemptStartedAt = microtime(true);

            $result = $this->client->complete(
                $provider['name'],
                $provider['key'],
                $provider['base_url'],
                $model,
                $systemPrompt,
                $userPrompt,
                $maxTokens,
                $attemptTimeout,
                $geminiJsonSchema,
            );

            if ($result === false) {
                $this->providerHealth->recordFailure(
                    $provider['name'],
                    $model,
                    $provider['key'],
                    true,
                );

                $blockedProviders[
                    $provider['name']
                ] = true;

                continue;
            }

            if ($result === null) {
                $this->providerHealth->recordFailure(
                    $provider['name'],
                    $model,
                    $provider['key'],
                );

                continue;
            }

            $normalized = $normalize(
                $result->content,
            );

            if (
                ! is_string($normalized)
                || trim($normalized) === ''
            ) {
                $this->providerHealth->recordFailure(
                    $provider['name'],
                    $model,
                    $provider['key'],
                );

                Log::warning(
                    $this->registry->label(
                        $provider['name'],
                    )
                        .' '.$logScope
                        .' response was rejected.',
                    [
                        'provider' => $provider['name'],
                        'requested_model' => $model,
                        'resolved_model' => $result->model,
                        'content' => Str::limit(
                            $result->content,
                            500,
                            '',
                        ),
                    ],
                );

                continue;
            }

            $this->providerHealth->recordSuccess(
                $provider['name'],
                $model,
                $provider['key'],
                (int) round(
                    (
                        microtime(true)
                        - $attemptStartedAt
                    ) * 1000,
                ),
            );

            $this->rateLimits->clear(
                $provider['name'],
                $provider['key'],
            );

            return new AiCompletionResult(
                $normalized,
                $result->model,
            );
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
                10,
            ),
        );
    }
}
