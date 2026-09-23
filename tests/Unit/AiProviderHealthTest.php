<?php

namespace Tests\Unit;

use App\Support\AiProviderHealth;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AiProviderHealthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config([
            'services.ai.provider_order' => [
                'openrouter',
                'xkiro',
                'gemini',
                'tokenrouter',
            ],
            'services.ai.health_cooldown_seconds' => 45,
            'services.ai.health_max_cooldown_seconds' => 300,
            'services.ai.health_state_seconds' => 600,
        ]);
    }

    public function test_healthy_providers_are_ordered_automatically(): void
    {
        $attempts = app(AiProviderHealth::class)
            ->orderedAttempts(
                $this->providers(),
            );

        $actual = collect($attempts)
            ->map(
                fn (array $attempt): string => (
                    $attempt['provider']['name']
                    .':'
                    .$attempt['model']
                ),
            )
            ->values()
            ->all();

        $this->assertSame(
            [
                'openrouter:openrouter-primary',
                'xkiro:xkiro-primary',
                'gemini:gemini-primary',
                'tokenrouter:tokenrouter-primary',
                'openrouter:openrouter-fallback',
                'xkiro:xkiro-fallback',
                'gemini:gemini-fallback',
            ],
            $actual,
        );
    }

    public function test_failed_model_is_temporarily_skipped(): void
    {
        $health = app(
            AiProviderHealth::class,
        );

        $health->recordFailure(
            'openrouter',
            'openrouter-primary',
            'openrouter-key',
        );

        $attempts = $health
            ->orderedAttempts(
                $this->providers(),
            );

        $actual = collect($attempts)
            ->map(
                fn (array $attempt): string => (
                    $attempt['provider']['name']
                    .':'
                    .$attempt['model']
                ),
            )
            ->values();

        $this->assertSame(
            'xkiro:xkiro-primary',
            $actual->first(),
        );

        $this->assertFalse(
            $actual->contains(
                'openrouter:openrouter-primary',
            ),
        );
    }

    public function test_success_restores_model_immediately(): void
    {
        $health = app(
            AiProviderHealth::class,
        );

        $health->recordFailure(
            'openrouter',
            'openrouter-primary',
            'openrouter-key',
        );

        $health->recordSuccess(
            'openrouter',
            'openrouter-primary',
            'openrouter-key',
            250,
        );

        $attempts = $health
            ->orderedAttempts(
                $this->providers(),
            );

        $this->assertSame(
            'openrouter',
            $attempts[0]['provider']['name'],
        );

        $this->assertSame(
            'openrouter-primary',
            $attempts[0]['model'],
        );
    }

    /**
     * @return list<array{
     *     name: string,
     *     key: string,
     *     base_url: string,
     *     models: list<string>
     * }>
     */
    private function providers(): array
    {
        return [
            [
                'name' => 'gemini',
                'key' => 'gemini-key',
                'base_url' => 'https://gemini.test',
                'models' => [
                    'gemini-primary',
                    'gemini-fallback',
                ],
            ],
            [
                'name' => 'openrouter',
                'key' => 'openrouter-key',
                'base_url' => 'https://openrouter.test',
                'models' => [
                    'openrouter-primary',
                    'openrouter-fallback',
                ],
            ],
            [
                'name' => 'tokenrouter',
                'key' => 'tokenrouter-key',
                'base_url' => 'https://tokenrouter.test',
                'models' => [
                    'tokenrouter-primary',
                ],
            ],
            [
                'name' => 'xkiro',
                'key' => 'xkiro-key',
                'base_url' => 'https://xkiro.test',
                'models' => [
                    'xkiro-primary',
                    'xkiro-fallback',
                ],
            ],
        ];
    }
}
