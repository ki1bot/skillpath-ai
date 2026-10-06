<?php

namespace Tests\Unit;

use App\Models\LearningMaterial;
use App\Models\Skill;
use App\Models\User;
use App\Services\AiInsightService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiOpenRouterFreeRoutingTest extends TestCase
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
            'services.juanrouter_backup.key' => 'test-backup-key',
            'services.juanrouter_backup.model' => 'deepseek-v4.1-flash',
            'services.juanrouter_backup.base_url' => 'https://backup.test/v1',
            'services.gemini.key' => null,
            'services.xkiro.key' => null,
            'services.juanrouter.key' => null,
        ]);

        Http::preventStrayRequests();
    }

    public function test_openrouter_free_uses_its_configured_alias_for_the_ui_label(): void
    {
        Http::fake(
            function ($request) {
                if (
                    $request->url()
                    !== 'https://openrouter.test/v1/chat/completions'
                ) {
                    return Http::response([], 404);
                }

                if (
                    $request['model']
                    === 'nex-agi/nex-n2.5-pro:free'
                ) {
                    return Http::response(
                        [
                            'error' => [
                                'message' => 'Primary unavailable',
                            ],
                        ],
                        503,
                    );
                }

                return Http::response(
                    [
                        'model' => 'meta-llama/example-free-model',
                        'choices' => [
                            [
                                'message' => [
                                    'role' => 'assistant',
                                    'content' => "1. Buat simulasi register sederhana.\n2. Dokumentasikan hasil latihan.\n3. Tambahkan edge case interrupt.",
                                ],
                                'finish_reason' => 'stop',
                            ],
                        ],
                    ],
                    200,
                );
            },
        );

        $result = app(AiInsightService::class)
            ->exerciseVariation(
                $this->user(),
                $this->material(),
            );

        $this->assertTrue(
            $result['generated_by_ai'],
        );

        $this->assertSame(
            'openrouter/free',
            $result['model'],
        );

        Http::assertSentCount(2);
    }

    public function test_content_safety_model_from_openrouter_free_is_rejected_and_deepseek_takes_over(): void
    {
        Http::fake(
            function ($request) {
                if (
                    $request->url()
                    === 'https://openrouter.test/v1/chat/completions'
                ) {
                    if (
                        $request['model']
                        === 'nex-agi/nex-n2.5-pro:free'
                    ) {
                        return Http::response(
                            [
                                'error' => [
                                    'message' => 'Primary unavailable',
                                ],
                            ],
                            503,
                        );
                    }

                    return Http::response(
                        [
                            'model' => 'nvidia/nemotron-3.5-content-safety:free',
                            'choices' => [
                                [
                                    'message' => [
                                        'role' => 'assistant',
                                        'content' => 'User Safety: safe',
                                    ],
                                    'finish_reason' => 'stop',
                                ],
                            ],
                        ],
                        200,
                    );
                }

                if (
                    $request->url()
                    === 'https://backup.test/v1/chat/completions'
                ) {
                    return Http::response(
                        [
                            'model' => 'deepseek-v4.1-flash',
                            'choices' => [
                                [
                                    'message' => [
                                        'role' => 'assistant',
                                        'content' => "1. Identifikasi register microprocessor.\n2. Dokumentasikan konfigurasi pin.\n3. Uji edge case interrupt.",
                                    ],
                                    'finish_reason' => 'stop',
                                ],
                            ],
                        ],
                        200,
                    );
                }

                return Http::response([], 404);
            },
        );

        $result = app(AiInsightService::class)
            ->exerciseVariation(
                $this->user(),
                $this->material(),
            );

        $this->assertTrue(
            $result['generated_by_ai'],
        );

        $this->assertSame(
            'deepseek-v4.1-flash',
            $result['model'],
        );

        $this->assertStringNotContainsString(
            'User Safety',
            (string) $result['content'],
        );

        Http::assertSentCount(3);
    }

    private function user(): User
    {
        $user = new User;
        $user->id = 7;

        return $user;
    }

    private function material(): LearningMaterial
    {
        $skill = new Skill;
        $skill->name = 'Microprocessor';

        $material = new LearningMaterial;
        $material->id = 11;
        $material->title = 'Microprocessor dan Microcontroller';
        $material->difficulty = 'beginner';
        $material->learning_objectives = [
            'Memahami register',
        ];
        $material->practice_task = 'Buat latihan register sederhana.';
        $material->setRelation('skill', $skill);

        return $material;
    }
}
