<?php

namespace Tests\Unit;

use App\Services\Ai\AiCompletionCoordinator;
use App\Services\Ai\AiProviderRegistry;
use App\Support\AiProviderHealth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JuanRouterBackupFailoverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config([
            'services.ai.provider_order' => [
                'juanrouter',
                'openrouter',
                'xkiro',
                'gemini',
                'juanrouter_backup',
            ],
            'services.ai.backup_providers' => [
                'juanrouter_backup',
            ],
            'services.ai.request_timeout' => 60,
            'services.ai.attempt_timeout' => 25,
            'services.ai.connect_timeout' => 5,
            'services.ai.failure_cache_seconds' => 10,
            'services.ai.health_cooldown_seconds' => 45,
            'services.ai.health_max_cooldown_seconds' => 300,
            'services.ai.health_state_seconds' => 600,

            'services.juanrouter.key' => 'test-primary-key',
            'services.juanrouter.model' => 'gpt-6-luna',
            'services.juanrouter.fallback_models' => [
                'gpt-5.6-luna',
            ],
            'services.juanrouter.base_url' => 'https://primary-router.test/v1',
            'services.juanrouter.reasoning_effort' => 'low',

            'services.juanrouter_backup.key' => 'test-backup-key',
            'services.juanrouter_backup.model' => 'deepseek-v4.1-flash',
            'services.juanrouter_backup.base_url' => 'https://backup-router.test/v1',

            'services.openrouter.key' => null,
            'services.xkiro.key' => null,
            'services.gemini.key' => null,
        ]);

        Http::preventStrayRequests();
    }

    public function test_backup_provider_is_used_after_primary_models_fail(): void
    {
        Http::fake([
            'https://primary-router.test/v1/chat/completions' => Http::response(
                [
                    'error' => [
                        'message' => 'Primary unavailable',
                    ],
                ],
                503,
            ),
            'https://backup-router.test/v1/chat/completions' => Http::response(
                [
                    'id' => 'backup-test',
                    'object' => 'chat.completion',
                    'model' => 'deepseek-v4.1-flash',
                    'choices' => [
                        [
                            'index' => 0,
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Respons cadangan berhasil.',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $result = app(
            AiCompletionCoordinator::class,
        )->complete(
            app(AiProviderRegistry::class)->configured(),
            'Instruksi sistem.',
            'Permintaan pengguna.',
            256,
            30,
            fn (string $content): ?string => trim($content) !== ''
                ? trim($content)
                : null,
            null,
            'backup failover test',
        );

        $this->assertNotNull($result);
        $this->assertSame(
            'Respons cadangan berhasil.',
            $result->content,
        );
        $this->assertSame(
            'deepseek-v4.1-flash',
            $result->model,
        );

        $models = collect(
            Http::recorded(),
        )
            ->map(
                fn (array $record) => $record[0]['model'],
            )
            ->values()
            ->all();

        $this->assertSame(
            [
                'gpt-6-luna',
                'gpt-5.6-luna',
                'deepseek-v4.1-flash',
            ],
            $models,
        );
    }

    public function test_backup_provider_is_not_promoted_ahead_of_healthy_primary(): void
    {
        config([
            'services.juanrouter.fallback_models' => [],
        ]);

        $health = app(
            AiProviderHealth::class,
        );

        $health->recordSuccess(
            'juanrouter',
            'gpt-6-luna',
            'test-primary-key',
            12000,
        );

        $health->recordSuccess(
            'juanrouter_backup',
            'deepseek-v4.1-flash',
            'test-backup-key',
            20,
        );

        Http::fake([
            'https://primary-router.test/v1/chat/completions' => Http::response(
                [
                    'id' => 'primary-test',
                    'object' => 'chat.completion',
                    'model' => 'gpt-6-luna',
                    'choices' => [
                        [
                            'index' => 0,
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Respons utama berhasil.',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
            'https://backup-router.test/v1/chat/completions' => Http::response(
                [
                    'id' => 'backup-test',
                    'object' => 'chat.completion',
                    'model' => 'deepseek-v4.1-flash',
                    'choices' => [
                        [
                            'index' => 0,
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Respons cadangan.',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $result = app(
            AiCompletionCoordinator::class,
        )->complete(
            app(AiProviderRegistry::class)->configured(),
            'Instruksi sistem.',
            'Permintaan pengguna.',
            256,
            30,
            fn (string $content): ?string => trim($content) !== ''
                ? trim($content)
                : null,
            null,
            'backup priority test',
        );

        $this->assertNotNull($result);
        $this->assertSame(
            'gpt-6-luna',
            $result->model,
        );

        Http::assertSentCount(1);

        Http::assertSent(
            fn ($request): bool => $request->url()
                === 'https://primary-router.test/v1/chat/completions'
                && $request['model'] === 'gpt-6-luna',
        );
    }

    public function test_public_chat_uses_backup_provider_after_primary_and_fallback_fail(): void
    {
        Cache::flush();

        config([
            'services.public_chat.enabled' => true,
            'services.public_chat.key' => 'test-public-chat-key',
            'services.public_chat.model' => 'gpt-6-luna',
            'services.public_chat.fallback_models' => [
                'gpt-5.6-luna',
            ],
            'services.public_chat.base_url' => 'https://chat-primary.test/v1',
            'services.public_chat.request_timeout' => 50,
            'services.public_chat.max_history_messages' => 8,

            'services.juanrouter_backup.key' => 'test-chat-backup-key',
            'services.juanrouter_backup.model' => 'deepseek-v4.1-flash',
            'services.juanrouter_backup.base_url' => 'https://chat-backup.test/v1',
        ]);

        Http::fake([
            'https://chat-primary.test/v1/chat/completions' => Http::sequence()
                ->push(
                    [
                        'error' => [
                            'message' => 'Primary unavailable',
                        ],
                    ],
                    503,
                )
                ->push(
                    [
                        'error' => [
                            'message' => 'Fallback unavailable',
                        ],
                    ],
                    503,
                ),
            'https://chat-backup.test/v1/chat/completions' => Http::response(
                [
                    'id' => 'public-chat-backup-test',
                    'object' => 'chat.completion',
                    'model' => 'deepseek-v4.1-flash',
                    'choices' => [
                        [
                            'index' => 0,
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'SkillPath membantu mahasiswa belajar secara lebih terarah.',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $this->postJson(
            '/bantuan/chat',
            [
                'message' => 'SkillPath itu apa?',
                'history' => [],
            ],
        )
            ->assertOk()
            ->assertJson([
                'message' => 'SkillPath membantu mahasiswa belajar secara lebih terarah.',
                'blocked' => false,
            ]);

        $records = collect(
            Http::recorded(),
        );

        $this->assertSame(
            [
                'gpt-6-luna',
                'gpt-5.6-luna',
                'deepseek-v4.1-flash',
            ],
            $records
                ->map(
                    fn (array $record) => $record[0]['model'],
                )
                ->values()
                ->all(),
        );

        $this->assertSame(
            [
                'https://chat-primary.test/v1/chat/completions',
                'https://chat-primary.test/v1/chat/completions',
                'https://chat-backup.test/v1/chat/completions',
            ],
            $records
                ->map(
                    fn (array $record) => $record[0]->url(),
                )
                ->values()
                ->all(),
        );
    }
}
