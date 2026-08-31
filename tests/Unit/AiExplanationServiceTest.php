<?php

namespace Tests\Unit;

use App\Models\Career;
use App\Models\User;
use App\Services\AiExplanationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiExplanationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config([
            'services.tokenrouter.key' => null,
            'services.xkiro.key' => null,
        ]);
    }

    public function test_no_explanation_is_returned_when_ai_providers_are_not_configured(): void
    {
        config([
            'services.gemini.key' => null,
            'services.openrouter.key' => null,
            'services.tokenrouter.key' => null,
            'services.xkiro.key' => null,
        ]);

        $result = app(AiExplanationService::class)
            ->skillGapSummary(
                $this->user(),
                $this->analysis(),
            );

        $this->assertFalse(
            $result->generatedByAi,
        );

        $this->assertNull(
            $result->model,
        );

        $this->assertNull(
            $result->summary,
        );
    }

    public function test_valid_indonesian_openrouter_response_is_displayed(): void
    {
        config([
            'services.gemini.key' => null,
            'services.openrouter.key' => 'test-openrouter-key',
            'services.openrouter.model' => 'minimax/minimax-m3:free',
            'services.openrouter.fallback_models' => [
                'openrouter/free',
            ],
            'services.openrouter.base_url' => 'https://openrouter.ai/api/v1',
        ]);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response(
                [
                    'id' => 'generation-test',
                    'model' => 'minimax/minimax-m3:free',
                    'choices' => [
                        [
                            'index' => 0,
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Database menjadi prioritas utama karena skor Anda masih 30 dari target 75. Kesenjangan ini perlu diprioritaskan sebelum kemampuan lanjutan yang bergantung pada pengelolaan data.',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $result = app(AiExplanationService::class)
            ->skillGapSummary(
                $this->user(),
                $this->analysis(),
            );

        $this->assertTrue(
            $result->generatedByAi,
        );

        $this->assertSame(
            'minimax/minimax-m3:free',
            $result->model,
        );

        $this->assertSame(
            'Database menjadi prioritas utama karena skor Anda masih 30 dari target 75. Kesenjangan ini perlu diprioritaskan sebelum kemampuan lanjutan yang bergantung pada pengelolaan data.',
            $result->summary,
        );

        Http::assertSent(
            fn ($request) => $request->url()
                === 'https://openrouter.ai/api/v1/chat/completions'
                && $request['model']
                === 'minimax/minimax-m3:free'
                && isset($request['provider']['allow_fallbacks']),
        );
    }

    public function test_valid_indonesian_tokenrouter_response_is_displayed(): void
    {
        config([
            'services.gemini.key' => null,
            'services.openrouter.key' => null,
            'services.tokenrouter.key' => 'test-tokenrouter-key',
            'services.tokenrouter.model' => 'z-ai/glm-5.3-free',
            'services.tokenrouter.fallback_models' => [],
            'services.tokenrouter.base_url' => 'https://api.tokenrouter.com/v1',
            'services.xkiro.key' => null,
        ]);

        Http::fake([
            'https://api.tokenrouter.com/v1/chat/completions' => Http::response(
                [
                    'id' => 'tokenrouter-test',
                    'model' => 'glm-5.3',
                    'choices' => [
                        [
                            'index' => 0,
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Database menjadi prioritas utama karena kemampuan saat ini masih 30 dari target 75. Kesenjangan tersebut perlu ditutup sebelum mempelajari kemampuan lanjutan.',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $result = app(AiExplanationService::class)
            ->skillGapSummary(
                $this->user(),
                $this->analysis(),
            );

        $this->assertTrue(
            $result->generatedByAi,
        );

        $this->assertSame(
            'glm-5.3',
            $result->model,
        );

        Http::assertSent(
            fn ($request) => $request->url()
                === 'https://api.tokenrouter.com/v1/chat/completions'
                && $request['model']
                === 'z-ai/glm-5.3-free'
                && ! isset($request['provider']),
        );
    }

    public function test_valid_indonesian_xkiro_response_is_displayed(): void
    {
        config([
            'services.gemini.key' => null,
            'services.openrouter.key' => null,
            'services.tokenrouter.key' => null,
            'services.xkiro.key' => 'test-xkiro-key',
            'services.xkiro.model' => 'deepseek/deepseek-v4-pro',
            'services.xkiro.fallback_models' => [
                'mistralai/mistral-large-2512',
            ],
            'services.xkiro.base_url' => 'https://api.xkiro.com/v1',
        ]);

        Http::fake([
            'https://api.xkiro.com/v1/chat/completions' => Http::response(
                [
                    'id' => 'xkiro-test',
                    'model' => 'deepseek/deepseek-v4-pro',
                    'choices' => [
                        [
                            'index' => 0,
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Database menjadi prioritas utama karena kemampuan saat ini masih 30 dari target 75. Kesenjangan tersebut perlu ditutup sebelum mempelajari kemampuan lanjutan.',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $result = app(AiExplanationService::class)
            ->skillGapSummary(
                $this->user(),
                $this->analysis(),
            );

        $this->assertTrue(
            $result->generatedByAi,
        );

        $this->assertSame(
            'deepseek/deepseek-v4-pro',
            $result->model,
        );

        Http::assertSent(
            fn ($request) => $request->url()
                === 'https://api.xkiro.com/v1/chat/completions'
                && $request['model']
                === 'deepseek/deepseek-v4-pro'
                && ! isset($request['provider']),
        );
    }

    public function test_gemini_three_uses_minimal_thinking_and_returns_the_final_answer(): void
    {
        config([
            'services.gemini.key' => 'test-gemini-key',
            'services.gemini.model' => 'gemini-3.6-flash',
            'services.gemini.fallback_models' => [],
            'services.gemini.base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'services.openrouter.key' => null,
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent' => Http::response(
                [
                    'candidates' => [
                        [
                            'content' => [
                                'parts' => [
                                    [
                                        'text' => 'Database menjadi prioritas utama karena kemampuan saat ini masih 30 dari target 75. Kesenjangan tersebut perlu ditutup sebelum mempelajari kemampuan lanjutan yang bergantung pada pengelolaan data.',
                                    ],
                                ],
                            ],
                            'finishReason' => 'STOP',
                        ],
                    ],
                    'modelVersion' => 'gemini-3.6-flash',
                ],
                200,
            ),
        ]);

        $result = app(AiExplanationService::class)
            ->skillGapSummary(
                $this->user(),
                $this->analysis(),
            );

        $this->assertTrue(
            $result->generatedByAi,
        );

        $this->assertSame(
            'gemini-3.6-flash',
            $result->model,
        );

        Http::assertSent(
            fn ($request) => $request->url()
                === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent'
                && $request['generationConfig']['maxOutputTokens'] === 1024
                && $request['generationConfig']['thinkingConfig']['thinkingLevel'] === 'minimal'
                && ! isset($request['generationConfig']['temperature'])
                && ! isset($request['generationConfig']['thinkingConfig']['thinkingBudget']),
        );
    }

    public function test_invalid_ai_response_is_not_replaced_with_system_text(): void
    {
        config([
            'services.gemini.key' => null,
            'services.openrouter.key' => 'test-openrouter-key',
            'services.openrouter.model' => 'minimax/minimax-m3:free',
            'services.openrouter.fallback_models' => [],
            'services.openrouter.base_url' => 'https://openrouter.ai/api/v1',
        ]);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response(
                [
                    'id' => 'generation-test',
                    'model' => 'minimax/minimax-m3:free',
                    'choices' => [
                        [
                            'index' => 0,
                            'message' => [
                                'role' => 'assistant',
                                'content' => 'Your current learning result should focus on the next skill because it is important for your career.',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $result = app(AiExplanationService::class)
            ->skillGapSummary(
                $this->user(),
                $this->analysis(),
            );

        $this->assertFalse(
            $result->generatedByAi,
        );

        $this->assertNull(
            $result->model,
        );

        $this->assertNull(
            $result->summary,
        );
    }

    public function test_gemini_tries_the_configured_fallback_model(): void
    {
        config([
            'services.gemini.key' => 'test-gemini-key',
            'services.gemini.model' => 'gemini-3.5-flash-lite',
            'services.gemini.fallback_models' => [
                'gemini-3.1-flash-lite',
            ],
            'services.gemini.base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'services.openrouter.key' => null,
        ]);

        Http::fake(
            function ($request) {
                if (
                    str_contains(
                        $request->url(),
                        'gemini-3.5-flash-lite',
                    )
                ) {
                    return Http::response(
                        [],
                        503,
                    );
                }

                return Http::response(
                    [
                        'candidates' => [
                            [
                                'content' => [
                                    'parts' => [
                                        [
                                            'text' => 'Database menjadi prioritas utama karena kemampuan saat ini masih 30 dari target 75. Kesenjangan tersebut perlu ditutup sebelum mempelajari kemampuan lanjutan.',
                                        ],
                                    ],
                                ],
                                'finishReason' => 'STOP',
                            ],
                        ],
                        'modelVersion' => 'gemini-3.1-flash-lite',
                    ],
                    200,
                );
            },
        );

        $result = app(AiExplanationService::class)
            ->skillGapSummary(
                $this->user(),
                $this->analysis(),
            );

        $this->assertTrue(
            $result->generatedByAi,
        );

        $this->assertSame(
            'gemini-3.1-flash-lite',
            $result->model,
        );

        Http::assertSentCount(2);
    }

    private function user(): User
    {
        $user = new User;
        $user->id = 10;
        $user->setRelation(
            'targetCareer',
            new Career([
                'name' => 'Backend Developer',
            ]),
        );

        return $user;
    }

    private function analysis(): array
    {
        return [
            [
                'name' => 'Database',
                'current' => 30.0,
                'target' => 75.0,
                'gap' => 45.0,
                'priority' => 54.0,
                'status' => 'kesenjangan_tinggi',
                'reason' => 'Database diprioritaskan karena skor Anda 30 dari target 75, sehingga masih ada gap 45 poin. Skill ini juga menjadi fondasi bagi 2 skill lanjutan.',
                'prerequisites' => [
                    [
                        'id' => 1,
                        'name' => 'Dasar Pemrograman',
                        'slug' => 'dasar-pemrograman',
                    ],
                ],
            ],
            [
                'name' => 'REST API',
                'current' => 20.0,
                'target' => 80.0,
                'gap' => 60.0,
                'priority' => 48.0,
                'status' => 'kesenjangan_tinggi',
                'reason' => 'REST API diprioritaskan karena skor Anda 20 dari target 80, sehingga masih ada gap 60 poin.',
                'prerequisites' => [
                    [
                        'id' => 2,
                        'name' => 'HTTP',
                        'slug' => 'http',
                    ],
                ],
            ],
        ];
    }
}
