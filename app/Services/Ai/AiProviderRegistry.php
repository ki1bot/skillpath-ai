<?php

namespace App\Services\Ai;

use Illuminate\Support\Str;

/**
 * @phpstan-type AiProvider array{
 *     name: string,
 *     key: string,
 *     base_url: string,
 *     models: list<string>
 * }
 * @phpstan-type AiProviderDefinition array{
 *     name: string,
 *     key: mixed,
 *     model: mixed,
 *     fallback_models: mixed,
 *     base_url: mixed
 * }
 */
class AiProviderRegistry
{
    /**
     * @return list<AiProvider>
     */
    public function configured(): array
    {
        $providers = [];

        foreach ($this->definitions() as $definition) {
            $provider = $this->makeProvider($definition);

            if ($provider !== null) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }

    /**
     * @return list<AiProvider>
     */
    public function forFeature(string $feature): array
    {
        $providerName = config(
            'services.ai.feature_providers.'.trim($feature),
        );

        if (
            ! is_string($providerName)
            || trim($providerName) === ''
        ) {
            return $this->configured();
        }

        $providerNames = [
            Str::lower(trim($providerName)),
        ];

        foreach ($this->backupProviderNames() as $backupProvider) {
            $providerNames[] = $backupProvider;
        }

        $providerNames = array_values(
            array_unique($providerNames),
        );

        /** @var array<string, AiProvider> $configured */
        $configured = [];

        foreach ($this->configured() as $provider) {
            $configured[
                Str::lower($provider['name'])
            ] = $provider;
        }

        $providers = [];

        foreach ($providerNames as $name) {
            if (! isset($configured[$name])) {
                continue;
            }

            $providers[] = $configured[$name];
        }

        return $providers;
    }

    /**
     * @param  list<AiProvider>  $providers
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
            'juanrouter_backup' => 'Juan Router Backup',
            'openrouter' => 'OpenRouter',
            'xkiro' => 'xKiro',
            'gemini' => 'Gemini',
            default => Str::headline($provider),
        };
    }

    public function isBackupProvider(?string $provider): bool
    {
        if (
            ! is_string($provider)
            || trim($provider) === ''
        ) {
            return false;
        }

        return in_array(
            Str::lower(trim($provider)),
            $this->backupProviderNames(),
            true,
        );
    }

    /**
     * @return list<string>
     */
    private function backupProviderNames(): array
    {
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
            return [];
        }

        $names = [];

        foreach ($backupProviders as $backupProvider) {
            if (
                ! is_string($backupProvider)
                || trim($backupProvider) === ''
            ) {
                continue;
            }

            $names[] = Str::lower(
                trim($backupProvider),
            );
        }

        return array_values(
            array_unique($names),
        );
    }

    /**
     * @return list<AiProviderDefinition>
     */
    private function definitions(): array
    {
        return [
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
            [
                'name' => 'juanrouter_backup',
                'key' => config('services.juanrouter_backup.key'),
                'model' => config(
                    'services.juanrouter_backup.model',
                    'deepseek-v4.1-flash',
                ),
                'fallback_models' => [],
                'base_url' => config(
                    'services.juanrouter_backup.base_url',
                    'https://router.juan.web.id/v1',
                ),
            ],
        ];
    }

    /**
     * @param  AiProviderDefinition  $definition
     * @return AiProvider|null
     */
    private function makeProvider(array $definition): ?array
    {
        if (
            ! is_string($definition['key'])
            || trim($definition['key']) === ''
            || ! is_string($definition['model'])
            || trim($definition['model']) === ''
            || ! is_string($definition['base_url'])
            || trim($definition['base_url']) === ''
        ) {
            return null;
        }

        $models = [
            trim($definition['model']),
        ];

        $fallbackModels = $definition['fallback_models'];

        if (is_array($fallbackModels)) {
            foreach ($fallbackModels as $fallbackModel) {
                if (
                    ! is_string($fallbackModel)
                    || trim($fallbackModel) === ''
                ) {
                    continue;
                }

                $models[] = trim($fallbackModel);
            }
        }

        return [
            'name' => $definition['name'],
            'key' => trim($definition['key']),
            'base_url' => trim($definition['base_url']),
            'models' => array_values(
                array_unique($models),
            ),
        ];
    }
}
