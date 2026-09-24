<?php

namespace App\Services\Ai;

use Illuminate\Support\Str;

class AiProviderRegistry
{
    /**
     * @return list<array{
     *     name: string,
     *     key: string,
     *     base_url: string,
     *     models: list<string>
     * }>
     */
    public function configured(): array
    {
        $definitions = [
            [
                'name' => 'juanrouter',
                'key' => config('services.juanrouter.key'),
                'model' => config(
                    'services.juanrouter.model',
                    'gpt-6-luna',
                ),
                'fallback_models' => config(
                    'services.juanrouter.fallback_models',
                    ['gpt-5.6-luna'],
                ),
                'base_url' => config(
                    'services.juanrouter.base_url',
                    'https://router.juan.web.id/v1',
                ),
            ],
            [
                'name' => 'gemini',
                'key' => config('services.gemini.key'),
                'model' => config(
                    'services.gemini.model',
                    'gemini-3.6-flash',
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
                    'nex-agi/nex-n2.5-pro:free',
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
                'name' => 'xkiro',
                'key' => config('services.xkiro.key'),
                'model' => config(
                    'services.xkiro.model',
                    'qwen/qwen3.8-max:free',
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
                    $definition['fallback_models'] as $fallbackModel
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

    /**
     * @param  list<array{
     *     name: string,
     *     key: string,
     *     base_url: string,
     *     models: list<string>
     * }>  $providers
     */
    public function signature(array $providers): string
    {
        $signature = '';

        foreach ($providers as $provider) {
            $signature .= $provider['name']
                .'|'
                .$provider['base_url']
                .'|'
                .implode(',', $provider['models'])
                .';';
        }

        return $signature;
    }

    public function label(string $provider): string
    {
        return match ($provider) {
            'juanrouter' => 'Juan Router',
            'openrouter' => 'OpenRouter',
            'xkiro' => 'xKiro',
            'gemini' => 'Gemini',
            default => Str::headline($provider),
        };
    }
}
