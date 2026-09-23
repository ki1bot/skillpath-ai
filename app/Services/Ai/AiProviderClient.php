<?php

namespace App\Services\Ai;

use App\Support\AiCompletionResult;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AiProviderClient
{
    public function __construct(
        private readonly AiRequestPayloadFactory $payloadFactory,
        private readonly AiRateLimitStore $rateLimits,
        private readonly AiProviderRegistry $registry,
    ) {}

    /**
     * @param  array<string, mixed>|null  $geminiJsonSchema
     */
    public function complete(
        string $provider,
        string $key,
        string $baseUrl,
        string $model,
        string $systemPrompt,
        string $userPrompt,
        int $maxTokens,
        int $timeoutSeconds,
        ?array $geminiJsonSchema = null,
    ): AiCompletionResult|false|null {
        if ($provider === 'gemini') {
            return $this->requestGemini(
                $key,
                $baseUrl,
                $model,
                $systemPrompt,
                $userPrompt,
                $maxTokens,
                $timeoutSeconds,
                $geminiJsonSchema,
            );
        }

        return $this->requestOpenAiCompatible(
            $provider,
            $key,
            $baseUrl,
            $model,
            $systemPrompt,
            $userPrompt,
            $maxTokens,
            $timeoutSeconds,
        );
    }

    /**
     * @param  array<string, mixed>|null  $jsonSchema
     */
    private function requestGemini(
        string $key,
        string $baseUrl,
        string $model,
        string $systemPrompt,
        string $userPrompt,
        int $maxTokens,
        int $timeoutSeconds,
        ?array $jsonSchema,
    ): AiCompletionResult|false|null {
        try {
            $generationConfig = $this->payloadFactory
                ->geminiGenerationConfig(
                    $model,
                    max(1024, $maxTokens),
                    $jsonSchema,
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
                            5,
                        ),
                    ),
                )
                ->timeout($timeoutSeconds)
                ->post(
                    rtrim($baseUrl, '/')
                        .'/models/'
                        .rawurlencode($model)
                        .':generateContent',
                    [
                        'systemInstruction' => [
                            'parts' => [
                                [
                                    'text' => $systemPrompt,
                                ],
                            ],
                        ],
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    [
                                        'text' => $userPrompt,
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
                    $this->rateLimits->remember(
                        'gemini',
                        $key,
                        $response->json(),
                        $response->header('Retry-After'),
                    );
                }

                Log::warning(
                    'Gemini AI request failed.',
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

            if ($content === null) {
                Log::warning(
                    'Gemini AI response did not contain text.',
                    [
                        'model' => $model,
                        'finish_reason' => $response->json(
                            'candidates.0.finishReason',
                        ),
                    ],
                );

                return null;
            }

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
                'Gemini AI request timed out or could not connect.',
                [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        } catch (Throwable $exception) {
            Log::warning(
                'Gemini AI request threw an exception.',
                [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        }
    }

    private function requestOpenAiCompatible(
        string $provider,
        string $key,
        string $baseUrl,
        string $model,
        string $systemPrompt,
        string $userPrompt,
        int $maxTokens,
        int $timeoutSeconds,
    ): AiCompletionResult|false|null {
        try {
            $payload = $this->payloadFactory
                ->openAiPayload(
                    $provider,
                    $model,
                    $systemPrompt,
                    $userPrompt,
                    $maxTokens,
                );

            $request = Http::withToken($key);

            if ($provider === 'openrouter') {
                $request = $request->withHeaders([
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
                            5,
                        ),
                    ),
                )
                ->timeout($timeoutSeconds)
                ->post(
                    rtrim($baseUrl, '/')
                        .'/chat/completions',
                    $payload,
                );

            if (! $response->successful()) {
                $responsePayload = $response->json();

                $rateLimited = $response->status() === 429;

                $blockProvider = $rateLimited
                    && $this->rateLimits
                        ->shouldBlockProvider(
                            $provider,
                            $responsePayload,
                        );

                if ($blockProvider) {
                    $this->rateLimits->remember(
                        $provider,
                        $key,
                        $responsePayload,
                        $response->header('Retry-After'),
                    );
                }

                Log::warning(
                    $this->registry->label($provider)
                        .' AI request failed.',
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

                return $blockProvider
                    ? false
                    : null;
            }

            $content = $response->json(
                'choices.0.message.content',
            );

            if (
                ! is_string($content)
                || trim($content) === ''
            ) {
                Log::warning(
                    $this->registry->label($provider)
                        .' AI response did not contain text.',
                    [
                        'provider' => $provider,
                        'model' => $model,
                        'finish_reason' => $response->json(
                            'choices.0.finish_reason',
                        ),
                    ],
                );

                return null;
            }

            $responseModel = $response->json('model');

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
                $this->registry->label($provider)
                    .' AI request timed out or could not connect.',
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
                $this->registry->label($provider)
                    .' AI request threw an exception.',
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

        return implode("\n", $texts);
    }
}
