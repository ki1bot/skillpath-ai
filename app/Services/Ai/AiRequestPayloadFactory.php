<?php

namespace App\Services\Ai;

use Illuminate\Support\Str;

class AiRequestPayloadFactory
{
    /**
     * @param  array<string, mixed>|null  $jsonSchema
     * @return array<string, mixed>
     */
    public function geminiGenerationConfig(
        string $model,
        int $maxOutputTokens,
        ?array $jsonSchema = null,
    ): array {
        $config = [
            'maxOutputTokens' => $maxOutputTokens,
        ];

        $thinkingLevel = $this
            ->geminiThinkingLevel($model);

        if ($thinkingLevel !== null) {
            $config['thinkingConfig'] = [
                'thinkingLevel' => $thinkingLevel,
            ];
        } else {
            $config['temperature'] = 0.2;

            if (
                $this->canDisableGeminiThinking(
                    $model,
                )
            ) {
                $config['thinkingConfig'] = [
                    'thinkingBudget' => 0,
                ];
            }
        }

        if ($jsonSchema !== null) {
            $config['responseMimeType'] = 'application/json';
            $config['responseJsonSchema'] = $jsonSchema;
        }

        return $config;
    }

    /**
     * @return array<string, mixed>
     */
    public function openAiPayload(
        string $provider,
        string $model,
        string $systemPrompt,
        string $userPrompt,
        int $maxTokens,
    ): array {
        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $userPrompt,
                ],
            ],
            'temperature' => 0.2,
            'max_tokens' => $maxTokens,
            'stream' => false,
        ];

        if (
            $provider === 'juanrouter'
            && Str::contains(
                Str::lower($model),
                [
                    'gpt-5.6',
                    'gpt-6',
                ],
            )
        ) {
            unset(
                $payload['temperature'],
                $payload['max_tokens'],
            );

            $payload['max_completion_tokens'] = max(
                1024,
                $maxTokens,
            );

            $reasoningEffort = trim(
                (string) config(
                    'services.juanrouter.reasoning_effort',
                    'low',
                ),
            );

            if ($reasoningEffort !== '') {
                $payload['reasoning_effort'] = $reasoningEffort;
            }
        }

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

        return $payload;
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

    private function geminiThinkingLevel(
        string $model,
    ): ?string {
        $model = Str::lower(trim($model));

        if (
            Str::contains(
                $model,
                [
                    'gemini-3.6-flash',
                    'gemini-3.5-flash',
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
}
