<?php

namespace Tests\Unit;

use App\Services\Ai\AiCompletionCoordinator;
use App\Services\Ai\AiProviderRegistry;
use App\Services\Ai\PublicProjectChatService;
use App\Support\AiProviderHealth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAutomaticBackupFailoverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config([
            'services.ai.feature_providers' => [
                'skills' => 'gemini',
                'progress' => 'xkiro',
                'materials' => 'openrouter',
                'projects' => 'juanrouter',
            ],

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
            'services.ai.health_cooldown_seconds' => 45,
            'services.ai.health_max_cooldown_seconds' => 300,
            'services.ai.health_state_seconds' => 600,

            'services.openrouter.key' => 'test-openrouter-key',
            'services.openrouter.model' => 'nex-agi/nex-n2.5-pro:free',
            'services.openrouter.fallback_models' => [
                'openrouter/free',
            ],
            'services.openrouter.base_url' => 'https://openrouter.test/v1',

            'services.juanrouter.key' => 'test-juan-key',
            'services.juanrouter.model' => 'gpt-6-luna',
            'services.juanrouter.fallback_models' => [
                'gpt-5.6-luna',
            ],
            'services.juanrouter.base_url' => 'https://juan.test/v1',
            'services.juanrouter.reasoning_effort' => 'low',

            'services.juanrouter_backup.key' => 'test-backup-key',
            'services.juanrouter_backup.model' => 'deepseek-v4.1-flash',
            'services.juanrouter_backup.base_url' => 'https://backup.test/v1',

            'services.public_chat.enabled' => true,
            'services.public_chat.key' => 'test-chat-key',
            'services.public_chat.model' => 'gpt-6-luna',
            'services.public_chat.fallback_models' => [
                'gpt-5.6-luna',
            ],
            'services.public_chat.base_url' => 'https://chat.test/v1',
            'services.public_chat.request_timeout' => 50,
            'services.public_chat.max_history_messages' => 8,

            'services.gemini.key' => null,
            'services.xkiro.key' => null,
        ]);

        Http::preventStrayRequests();
    }

    public function test_material_automatically_uses_backup_even_when_backup_is_cooling_down(): void
    {
        app(AiProviderHealth::class)->recordFailure(
            'juanrouter_backup',
            'deepseek-v4.1-flash',
            'test-backup-key',
        );

        Http::fake([
            'https://openrouter.test/v1/chat/completions' => Http::sequence()
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

            'https://backup.test/v1/chat/completions' => Http::response(
                [
                    'model' => 'deepseek-v4.1-flash',

                    'choices' => [
                        [
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Cadangan otomatis berhasil.',
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
            app(AiProviderRegistry::class)->forFeature(
                'materials',
            ),
            'Instruksi sistem.',
            'Permintaan pengguna.',
            256,
            60,
            fn (string $content): ?string => trim($content) !== ''
                ? trim($content)
                : null,
            null,
            'automatic material backup test',
        );

        $this->assertNotNull($result);

        $this->assertSame(
            'deepseek-v4.1-flash',
            $result->model,
        );

        $this->assertSame(
            'Cadangan otomatis berhasil.',
            $result->content,
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
                'nex-agi/nex-n2.5-pro:free',
                'openrouter/free',
                'deepseek-v4.1-flash',
            ],
            $models,
        );
    }

    public function test_public_chat_automatically_uses_backup_even_when_backup_is_cooling_down(): void
    {
        app(AiProviderHealth::class)->recordFailure(
            'juanrouter_backup',
            'deepseek-v4.1-flash',
            'test-backup-key',
        );

        Http::fake([
            'https://chat.test/v1/chat/completions' => Http::sequence()
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

            'https://backup.test/v1/chat/completions' => Http::response(
                [
                    'model' => 'deepseek-v4.1-flash',

                    'choices' => [
                        [
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'SkillPath membantu proses belajar menjadi lebih terarah.',
                            ],

                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $result = app(
            PublicProjectChatService::class,
        )->reply(
            'Apa itu SkillPath?',
        );

        $this->assertTrue(
            $result['available'],
        );

        $this->assertFalse(
            $result['blocked'],
        );

        $this->assertSame(
            'deepseek-v4.1-flash',
            $result['model'],
        );

        $this->assertSame(
            'SkillPath membantu proses belajar menjadi lebih terarah.',
            $result['message'],
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
}
