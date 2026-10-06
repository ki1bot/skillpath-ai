<?php

namespace Tests\Unit;

use App\Services\Ai\AiProviderRegistry;
use Tests\TestCase;

class AiFeatureProviderRoutingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.ai.feature_providers' => [
                'skills' => 'gemini',
                'progress' => 'xkiro',
                'materials' => 'openrouter',
                'projects' => 'juanrouter',
            ],

            'services.ai.backup_providers' => [
                'juanrouter_backup',
            ],

            'services.gemini.key' => 'test-gemini-key',
            'services.gemini.model' => 'gemini-3.6-flash',
            'services.gemini.fallback_models' => [
                'gemini-3.5-flash-lite',
            ],
            'services.gemini.base_url' => 'https://gemini.test/v1beta',

            'services.xkiro.key' => 'test-xkiro-key',
            'services.xkiro.model' => 'qwen/qwen3.8-max:free',
            'services.xkiro.fallback_models' => [
                'mistralai/mistral-large-2512',
            ],
            'services.xkiro.base_url' => 'https://xkiro.test/v1',

            'services.openrouter.key' => 'test-openrouter-key',
            'services.openrouter.model' => 'nex-agi/nex-n2.5-pro:free',
            'services.openrouter.fallback_models' => [
                'openrouter/free',
            ],
            'services.openrouter.base_url' => 'https://openrouter.test/v1',

            'services.juanrouter.key' => 'test-juanrouter-key',
            'services.juanrouter.model' => 'gpt-6-luna',
            'services.juanrouter.fallback_models' => [
                'gpt-5.6-luna',
            ],
            'services.juanrouter.base_url' => 'https://juanrouter.test/v1',

            'services.juanrouter_backup.key' => 'test-backup-key',
            'services.juanrouter_backup.model' => 'deepseek-v4.1-flash',
            'services.juanrouter_backup.base_url' => 'https://backup.test/v1',
        ]);
    }

    public function test_each_feature_uses_its_dedicated_provider_then_backup(): void
    {
        $registry = app(AiProviderRegistry::class);

        $this->assertSame(
            [
                'gemini',
                'juanrouter_backup',
            ],
            $this->names(
                $registry->forFeature('skills'),
            ),
        );

        $this->assertSame(
            [
                'xkiro',
                'juanrouter_backup',
            ],
            $this->names(
                $registry->forFeature('progress'),
            ),
        );

        $this->assertSame(
            [
                'openrouter',
                'juanrouter_backup',
            ],
            $this->names(
                $registry->forFeature('materials'),
            ),
        );

        $this->assertSame(
            [
                'juanrouter',
                'juanrouter_backup',
            ],
            $this->names(
                $registry->forFeature('projects'),
            ),
        );
    }

    public function test_each_feature_keeps_its_provider_model_order(): void
    {
        $registry = app(AiProviderRegistry::class);

        $this->assertSame(
            [
                'gemini-3.6-flash',
                'gemini-3.5-flash-lite',
            ],
            $registry->forFeature('skills')[0]['models'],
        );

        $this->assertSame(
            [
                'qwen/qwen3.8-max:free',
                'mistralai/mistral-large-2512',
            ],
            $registry->forFeature('progress')[0]['models'],
        );

        $this->assertSame(
            [
                'nex-agi/nex-n2.5-pro:free',
                'openrouter/free',
            ],
            $registry->forFeature('materials')[0]['models'],
        );

        $this->assertSame(
            [
                'gpt-6-luna',
                'gpt-5.6-luna',
            ],
            $registry->forFeature('projects')[0]['models'],
        );

        $this->assertSame(
            [
                'deepseek-v4.1-flash',
            ],
            $registry->forFeature('projects')[1]['models'],
        );
    }

    public function test_backup_provider_is_identified_for_temporary_failover(): void
    {
        $registry = app(AiProviderRegistry::class);

        $this->assertTrue(
            $registry->isBackupProvider(
                'juanrouter_backup',
            ),
        );

        $this->assertFalse(
            $registry->isBackupProvider(
                'xkiro',
            ),
        );
    }

    public function test_backup_remains_available_when_dedicated_provider_is_not_configured(): void
    {
        config([
            'services.xkiro.key' => null,
        ]);

        $providers = app(
            AiProviderRegistry::class,
        )->forFeature('progress');

        $this->assertSame(
            [
                'juanrouter_backup',
            ],
            $this->names($providers),
        );
    }

    private function names(array $providers): array
    {
        return array_values(
            array_map(
                fn (array $provider): string => $provider['name'],
                $providers,
            ),
        );
    }
}
