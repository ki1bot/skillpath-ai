<?php

namespace Tests\Unit;

use App\Support\AiProviderHealth;
use Illuminate\Support\Carbon;
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

        Carbon::setTestNow(
            Carbon::parse(
                '2026-09-24 12:00:00',
            ),
        );
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_cold_start_uses_configured_order_only_as_tie_breaker(): void
    {
        $attempts = app(
            AiProviderHealth::class,
        )->orderedAttempts(
            $this->providers(),
        );

        $actual = collect(
            $attempts,
        )
            ->map(
                fn (array $attempt): string => (
                    $attempt[
                        'provider'
                    ][
                        'name'
                    ]
                    .':'
                    .$attempt[
                        'model'
                    ]
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

    public function test_success_latency_reorders_healthy_providers_automatically(): void
    {
        $health = app(
            AiProviderHealth::class,
        );

        $health->recordSuccess(
            'openrouter',
            'openrouter-primary',
            'openrouter-key',
            7000,
        );

        $health->recordSuccess(
            'xkiro',
            'xkiro-primary',
            'xkiro-key',
            600,
        );

        $health->recordSuccess(
            'gemini',
            'gemini-primary',
            'gemini-key',
            2500,
        );

        $attempts = $health
            ->orderedAttempts(
                $this->providers(),
            );

        $this->assertSame(
            'xkiro',
            $attempts[0][
                'provider'
            ][
                'name'
            ],
        );

        $this->assertSame(
            'gemini',
            $attempts[1][
                'provider'
            ][
                'name'
            ],
        );

        $this->assertSame(
            'openrouter',
            $attempts[2][
                'provider'
            ][
                'name'
            ],
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

        $actual = collect(
            $attempts,
        )
            ->map(
                fn (array $attempt): string => (
                    $attempt[
                        'provider'
                    ][
                        'name'
                    ]
                    .':'
                    .$attempt[
                        'model'
                    ]
                ),
            )
            ->values();

        $this->assertFalse(
            $actual->contains(
                'openrouter:openrouter-primary',
            ),
        );
    }

    public function test_failed_model_is_probed_again_automatically_after_cooldown(): void
    {
        $health = app(
            AiProviderHealth::class,
        );

        $health->recordFailure(
            'openrouter',
            'openrouter-primary',
            'openrouter-key',
        );

        Carbon::setTestNow(
            now()->addSeconds(
                46,
            ),
        );

        $attempts = $health
            ->orderedAttempts(
                $this->providers(),
            );

        $this->assertSame(
            'openrouter',
            $attempts[0][
                'provider'
            ][
                'name'
            ],
        );

        $this->assertSame(
            'openrouter-primary',
            $attempts[0][
                'model'
            ],
        );
    }

    public function test_success_restores_model_and_uses_measured_latency(): void
    {
        $health = app(
            AiProviderHealth::class,
        );

        $health->recordFailure(
            'openrouter',
            'openrouter-primary',
            'openrouter-key',
        );

        Carbon::setTestNow(
            now()->addSeconds(
                46,
            ),
        );

        $health->recordSuccess(
            'openrouter',
            'openrouter-primary',
            'openrouter-key',
            5000,
        );

        $health->recordSuccess(
            'xkiro',
            'xkiro-primary',
            'xkiro-key',
            500,
        );

        $attempts = $health
            ->orderedAttempts(
                $this->providers(),
            );

        $this->assertSame(
            'xkiro',
            $attempts[0][
                'provider'
            ][
                'name'
            ],
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
