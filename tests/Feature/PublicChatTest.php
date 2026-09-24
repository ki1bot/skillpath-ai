<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config([
            'services.public_chat.enabled' => true,
            'services.public_chat.key' => 'test-public-chat-key',
            'services.public_chat.model' => 'gpt-6-luna',
            'services.public_chat.fallback_models' => [
                'gpt-5.6-luna',
            ],
            'services.public_chat.base_url' => 'https://router.juan.web.id/v1',
            'services.public_chat.request_timeout' => 50,
            'services.public_chat.max_history_messages' => 8,

            'services.ai.provider_order' => [
                'juanrouter',
                'openrouter',
                'xkiro',
                'gemini',
            ],

            'services.ai.request_timeout' => 60,
            'services.ai.attempt_timeout' => 25,
            'services.ai.connect_timeout' => 5,
            'services.ai.failure_cache_seconds' => 10,
            'services.ai.health_cooldown_seconds' => 45,
            'services.ai.health_max_cooldown_seconds' => 300,
            'services.ai.health_state_seconds' => 600,

            'services.juanrouter.key' => 'test-juanrouter-key',
            'services.juanrouter.model' => 'gpt-6-luna',
            'services.juanrouter.fallback_models' => [
                'gpt-5.6-luna',
            ],
            'services.juanrouter.base_url' => 'https://router.juan.web.id/v1',
            'services.juanrouter.reasoning_effort' => 'low',

            'services.openrouter.key' => null,
            'services.xkiro.key' => null,
            'services.gemini.key' => null,
        ]);

        Http::preventStrayRequests();
    }

    public function test_guest_can_ask_about_skillpath_from_public_chat(): void
    {
        Http::fake([
            'https://router.juan.web.id/v1/chat/completions' => Http::response(
                [
                    'id' => 'public-chat-test',
                    'object' => 'chat.completion',
                    'model' => 'gpt-6-luna',

                    'choices' => [
                        [
                            'index' => 0,

                            'message' => [
                                'role' => 'assistant',
                                'content' => 'SkillPath membantu mahasiswa memahami kemampuan dan menentukan langkah belajar berikutnya dengan lebih terarah.',
                            ],

                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $response = $this->postJson(
            '/bantuan/chat',
            [
                'message' => 'SkillPath itu apa?',
                'history' => [],
            ],
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'SkillPath membantu mahasiswa memahami kemampuan dan menentukan langkah belajar berikutnya dengan lebih terarah.',
                'blocked' => false,
            ]);

        Http::assertSent(
            function ($request): bool {
                return $request->url()
                    === 'https://router.juan.web.id/v1/chat/completions'
                    && $request['model']
                        === 'gpt-6-luna'
                    && $request[
                        'reasoning_effort'
                    ] === 'low'
                    && $request[
                        'max_completion_tokens'
                    ] === 1024
                    && $request['stream']
                        === false
                    && ! isset(
                        $request['temperature'],
                    )
                    && ! isset(
                        $request['max_tokens'],
                    );
            },
        );
    }

    public function test_public_chat_does_not_answer_assessment_roadmap_or_project_work(): void
    {
        Http::fake();

        $response = $this->postJson(
            '/bantuan/chat',
            [
                'message' => 'Tolong jawabkan soal assessment nomor 1 dan buatkan roadmap saya.',
                'history' => [],
            ],
        );

        $response
            ->assertOk()
            ->assertJson([
                'blocked' => true,
            ]);

        Http::assertNothingSent();
    }

    public function test_authenticated_user_cannot_use_public_chat(): void
    {
        Http::fake();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(
                '/bantuan/chat',
                [
                    'message' => 'Apa itu SkillPath?',
                    'history' => [],
                ],
            )
            ->assertNotFound();

        Http::assertNothingSent();
    }

    public function test_public_chat_uses_juan_router_fallback_model_when_primary_fails(): void
    {
        Http::fake([
            'https://router.juan.web.id/v1/chat/completions' => Http::sequence()
                ->push(
                    [
                        'error' => [
                            'message' => 'primary model unavailable',
                        ],
                    ],
                    503,
                )
                ->push(
                    [
                        'id' => 'public-chat-fallback-test',
                        'object' => 'chat.completion',
                        'model' => 'gpt-5.6-luna',

                        'choices' => [
                            [
                                'index' => 0,

                                'message' => [
                                    'role' => 'assistant',
                                    'content' => 'SkillPath adalah platform pembelajaran terarah untuk membantu mahasiswa memahami kemampuan mereka.',
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
                'message' => 'Jelaskan fungsi SkillPath secara singkat.',
                'history' => [],
            ],
        )
            ->assertOk()
            ->assertJson([
                'blocked' => false,
            ]);

        Http::assertSentCount(2);

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
            ],
            $models,
        );
    }
}
