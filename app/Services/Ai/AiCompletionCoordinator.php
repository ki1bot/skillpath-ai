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
        bool $forceRetry = false,
    ): ?AiCompletionResult {
        if ($providers === []) {
            return null;
        }

        $deadline = microtime(true) + max(
            3,
            $requestTimeout,
        );

        $blockedProviders = [];
        $regularAttempts = [];
        $backupAttempts = [];
        $backupAttemptKeys = [];

        $backupProviders = config(
            'services.ai.backup_providers',
            ['juanrouter_backup'],
        );

        if (is_string($backupProviders)) {
            $backupProviders = explode(
                ',',
                $backupProviders,
            );
        }

        if (! is_array($backupProviders)) {
            $backupProviders = [];
        }

        $normalizedBackupProviders = [];

        foreach ($backupProviders as $backupProvider) {
            if (
                ! is_string($backupProvider)
                || trim($backupProvider) === ''
            ) {
                continue;
            }

            $normalizedBackupProviders[] = Str::lower(
                trim($backupProvider),
            );
        }

        $normalizedBackupProviders = array_values(
            array_unique($normalizedBackupProviders),
        );

        foreach (
            $this->providerHealth
                ->orderedAttempts(
                    $providers,
                    $forceRetry,
                ) as $attempt
        ) {
            $providerName = Str::lower(
                trim(
                    $attempt['provider']['name'],
                ),
            );

            if (
                in_array(
                    $providerName,
                    $normalizedBackupProviders,
                    true,
                )
            ) {
                $backupAttempts[] = $attempt;

                $backupAttemptKeys[
                    $this->attemptKey($attempt)
                ] = true;

                continue;
            }

            $regularAttempts[] = $attempt;
        }

        if ($normalizedBackupProviders !== []) {
            foreach (
                $this->providerHealth
                    ->orderedAttempts(
                        $providers,
                        true,
                    ) as $attempt
            ) {
                $providerName = Str::lower(
                    trim(
                        $attempt['provider']['name'],
                    ),
                );

                if (
                    ! in_array(
                        $providerName,
                        $normalizedBackupProviders,
                        true,
                    )
                ) {
                    continue;
                }

                $attemptKey = $this->attemptKey($attempt);

                if (isset($backupAttemptKeys[$attemptKey])) {
                    continue;
                }

                $backupAttempts[] = $attempt;
                $backupAttemptKeys[$attemptKey] = true;
            }
        }

        $attempts = [
            ...$regularAttempts,
            ...$backupAttempts,
        ];

        foreach ($attempts as $attemptIndex => $attempt) {
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
                ->nextAttemptTimeout(
                    $deadline,
                    count($attempts) - $attemptIndex,
                );

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

            if (
                $this->isUnsupportedResolvedModel(
                    $provider['name'],
                    $model,
                    $result->model,
                )
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
                        .' resolved an unsuitable model.',
                    [
                        'provider' => $provider['name'],
                        'requested_model' => $model,
                        'resolved_model' => $result->model,
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

            $normalized = $normalize(
                $result->content,
            );

            if (
                ! is_string($normalized)
                || trim($normalized) === ''
            ) {
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

            return new AiCompletionResult(
                $normalized,
                $model,
                $provider['name'],
            );
        }

        return null;
    }

    /**
     * @param  array{
     *     provider: array{
     *         name: string,
     *         key: string,
     *         base_url: string,
     *         models: list<string>
     *     },
     *     model: string
     * }  $attempt
     */
    private function attemptKey(array $attempt): string
    {
        return Str::lower(
            trim(
                $attempt['provider']['name'],
            ),
        )
            .'|'
            .trim($attempt['model']);
    }

    private function isUnsupportedResolvedModel(
        string $provider,
        string $requestedModel,
        string $resolvedModel,
    ): bool {
        if (
            Str::lower(trim($provider)) !== 'openrouter'
            || Str::lower(trim($requestedModel)) !== 'openrouter/free'
        ) {
            return false;
        }

        $resolvedModel = Str::lower(
            trim($resolvedModel),
        );

        return Str::contains(
            $resolvedModel,
            [
                'content-safety',
                'moderation',
                'guardrail',
            ],
        );
    }

    private function nextAttemptTimeout(
        float $deadline,
        int $remainingAttempts,
    ): ?int {
        $remainingSeconds = (int) floor(
            $deadline - microtime(true),
        );

        if ($remainingSeconds < 1) {
            return null;
        }

        $attemptLimit = max(
            1,
            (int) config(
                'services.ai.attempt_timeout',
                10,
            ),
        );

        $fairShare = max(
            1,
            intdiv(
                $remainingSeconds,
                max(1, $remainingAttempts),
            ),
        );

        return min(
            $remainingSeconds,
            $attemptLimit,
            $fairShare,
        );
    }
}
