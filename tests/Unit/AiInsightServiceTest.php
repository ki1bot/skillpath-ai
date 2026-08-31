<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AiInsightService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiInsightServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config([
            'services.tokenrouter.key' => null,
            'services.xkiro.key' => null,
        ]);
    }

    public function test_ai_insights_do_not_inject_default_text_when_api_key_is_not_configured(): void
    {
        config([
            'services.gemini.key' => null,
            'services.openrouter.key' => null,
            'services.tokenrouter.key' => null,
            'services.xkiro.key' => null,
        ]);

        $user = User::factory()->create([
            'weekly_study_hours' => 6,
        ]);

        $result = app(AiInsightService::class)
            ->progress(
                $user,
                [
                    'score' => 25,
                    'skill_mastery' => 30,
                    'roadmap_completion' => 10,
                    'project_score' => 0,
                    'consistency' => 20,
                    'evaluation_score' => 0,
                ],
            );

        $this->assertFalse(
            $result['generated_by_ai'],
        );

        $this->assertNull(
            $result['model'],
        );

        $this->assertNull(
            $result['progress'],
        );

        $this->assertNull(
            $result['schedule'],
        );

        $this->assertNull(
            $result['obstacles'],
        );
    }

    public function test_ai_insights_can_use_openrouter_chat_completion_response(): void
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
                                'content' => '<PROGRESS>Perkembangan AI berhasil dibuat.</PROGRESS>'
                                    .'<SCHEDULE>Jadwal AI berhasil dibuat.</SCHEDULE>'
                                    .'<OBSTACLES>Kendala AI berhasil dikelompokkan.</OBSTACLES>',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $user = User::factory()->create([
            'weekly_study_hours' => 6,
        ]);

        $result = app(AiInsightService::class)
            ->progress(
                $user,
                [
                    'score' => 25,
                    'skill_mastery' => 30,
                    'roadmap_completion' => 10,
                    'project_score' => 0,
                    'consistency' => 20,
                    'evaluation_score' => 0,
                ],
            );

        $this->assertTrue(
            $result['generated_by_ai'],
        );

        $this->assertSame(
            'minimax/minimax-m3:free',
            $result['model'],
        );

        $this->assertSame(
            'Perkembangan AI berhasil dibuat.',
            $result['progress'],
        );

        $this->assertSame(
            'Jadwal AI berhasil dibuat.',
            $result['schedule'],
        );

        $this->assertSame(
            'Kendala AI berhasil dikelompokkan.',
            $result['obstacles'],
        );

        Http::assertSent(
            fn ($request) => $request->url()
                === 'https://openrouter.ai/api/v1/chat/completions'
                && $request['model']
                === 'minimax/minimax-m3:free'
                && isset($request['provider']['allow_fallbacks']),
        );
    }

    public function test_ai_insights_can_use_tokenrouter_chat_completion_response(): void
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
                                'content' => '<PROGRESS>Perkembangan dari TokenRouter berhasil dibuat.</PROGRESS>'
                                    .'<SCHEDULE>Jadwal dari TokenRouter berhasil dibuat.</SCHEDULE>'
                                    .'<OBSTACLES>Kendala dari TokenRouter berhasil dikelompokkan.</OBSTACLES>',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $user = User::factory()->create([
            'weekly_study_hours' => 6,
        ]);

        $result = app(AiInsightService::class)
            ->progress(
                $user,
                [
                    'score' => 25,
                    'skill_mastery' => 30,
                    'roadmap_completion' => 10,
                    'project_score' => 0,
                    'consistency' => 20,
                    'evaluation_score' => 0,
                ],
            );

        $this->assertTrue(
            $result['generated_by_ai'],
        );

        $this->assertSame(
            'glm-5.3',
            $result['model'],
        );

        $this->assertSame(
            'Perkembangan dari TokenRouter berhasil dibuat.',
            $result['progress'],
        );

        Http::assertSent(
            fn ($request) => $request->url()
                === 'https://api.tokenrouter.com/v1/chat/completions'
                && $request['model']
                === 'z-ai/glm-5.3-free'
                && ! isset($request['provider']),
        );
    }

    public function test_ai_insights_can_use_xkiro_chat_completion_response(): void
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
                                'content' => '<PROGRESS>Perkembangan dari xKiro berhasil dibuat.</PROGRESS>'
                                    .'<SCHEDULE>Jadwal dari xKiro berhasil dibuat.</SCHEDULE>'
                                    .'<OBSTACLES>Kendala dari xKiro berhasil dikelompokkan.</OBSTACLES>',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $user = User::factory()->create([
            'weekly_study_hours' => 6,
        ]);

        $result = app(AiInsightService::class)
            ->progress(
                $user,
                [
                    'score' => 25,
                    'skill_mastery' => 30,
                    'roadmap_completion' => 10,
                    'project_score' => 0,
                    'consistency' => 20,
                    'evaluation_score' => 0,
                ],
            );

        $this->assertTrue(
            $result['generated_by_ai'],
        );

        $this->assertSame(
            'deepseek/deepseek-v4-pro',
            $result['model'],
        );

        $this->assertSame(
            'Perkembangan dari xKiro berhasil dibuat.',
            $result['progress'],
        );

        Http::assertSent(
            fn ($request) => $request->url()
                === 'https://api.xkiro.com/v1/chat/completions'
                && $request['model']
                === 'deepseek/deepseek-v4-pro'
                && ! isset($request['provider']),
        );
    }

    public function test_gemini_three_progress_uses_minimal_thinking_and_enough_output_tokens(): void
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
                                        'text' => json_encode([
                                            'progress' => 'Perkembangan belajar sudah tercatat dan dapat ditingkatkan secara bertahap.',
                                            'schedule' => 'Gunakan jadwal belajar mingguan untuk menyelesaikan materi berikutnya.',
                                            'obstacles' => 'Kendala yang tercatat perlu ditinjau sebelum sesi belajar berikutnya.',
                                        ], JSON_THROW_ON_ERROR),
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

        $user = User::factory()->create([
            'weekly_study_hours' => 6,
        ]);

        $result = app(AiInsightService::class)
            ->progress(
                $user,
                [
                    'score' => 25,
                    'skill_mastery' => 30,
                    'roadmap_completion' => 10,
                    'project_score' => 0,
                    'consistency' => 20,
                    'evaluation_score' => 0,
                ],
            );

        $this->assertTrue(
            $result['generated_by_ai'],
        );

        $this->assertSame(
            'gemini-3.6-flash',
            $result['model'],
        );

        Http::assertSent(
            fn ($request) => $request->url()
                === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent'
                && $request['generationConfig']['maxOutputTokens'] === 1024
                && $request['generationConfig']['responseMimeType'] === 'application/json'
                && $request['generationConfig']['responseJsonSchema']['required'] === [
                    'progress',
                    'schedule',
                    'obstacles',
                ]
                && $request['generationConfig']['thinkingConfig']['thinkingLevel'] === 'minimal'
                && ! isset($request['generationConfig']['temperature'])
                && ! isset($request['generationConfig']['thinkingConfig']['thinkingBudget']),
        );
    }

    public function test_ai_insights_try_configured_fallback_model_when_primary_model_fails(): void
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

        Http::fake(
            function ($request) {
                if (
                    $request['model']
                    === 'minimax/minimax-m3:free'
                ) {
                    return Http::response(
                        [],
                        503,
                    );
                }

                return Http::response(
                    [
                        'id' => 'generation-fallback',
                        'model' => 'nvidia/nemotron-3-ultra-550b-a55b:free',
                        'choices' => [
                            [
                                'index' => 0,
                                'message' => [
                                    'role' => 'assistant',
                                    'content' => '<PROGRESS>Perkembangan dari model fallback berhasil dibuat.</PROGRESS>'
                                        .'<SCHEDULE>Jadwal dari model fallback berhasil dibuat.</SCHEDULE>'
                                        .'<OBSTACLES>Kendala dari model fallback berhasil dibuat.</OBSTACLES>',
                                ],
                                'finish_reason' => 'stop',
                            ],
                        ],
                    ],
                    200,
                );
            },
        );

        $user = User::factory()->create([
            'weekly_study_hours' => 6,
        ]);

        $result = app(AiInsightService::class)
            ->progress(
                $user,
                [
                    'score' => 25,
                    'skill_mastery' => 30,
                    'roadmap_completion' => 10,
                    'project_score' => 0,
                    'consistency' => 20,
                    'evaluation_score' => 0,
                ],
            );

        $this->assertTrue(
            $result['generated_by_ai'],
        );

        $this->assertSame(
            'nvidia/nemotron-3-ultra-550b-a55b:free',
            $result['model'],
        );

        $this->assertSame(
            'Perkembangan dari model fallback berhasil dibuat.',
            $result['progress'],
        );

        Http::assertSentCount(2);

        Http::assertSent(
            fn ($request) => $request['model']
                === 'minimax/minimax-m3:free',
        );

        Http::assertSent(
            fn ($request) => $request['model']
                === 'openrouter/free',
        );
    }

    public function test_english_ai_response_is_not_displayed(): void
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
                                'content' => '<PROGRESS>Your current learning progress is improving based on the recorded results.</PROGRESS>'
                                    .'<SCHEDULE>You should use your weekly learning time for the next materials.</SCHEDULE>'
                                    .'<OBSTACLES>Your recorded obstacles should be reviewed before the next step.</OBSTACLES>',
                            ],
                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $user = User::factory()->create([
            'weekly_study_hours' => 6,
        ]);

        $result = app(AiInsightService::class)
            ->progress(
                $user,
                [
                    'score' => 25,
                    'skill_mastery' => 30,
                    'roadmap_completion' => 10,
                    'project_score' => 0,
                    'consistency' => 20,
                    'evaluation_score' => 0,
                ],
            );

        $this->assertFalse(
            $result['generated_by_ai'],
        );

        $this->assertNull(
            $result['model'],
        );

        $this->assertNull(
            $result['progress'],
        );

        $this->assertNull(
            $result['schedule'],
        );

        $this->assertNull(
            $result['obstacles'],
        );
    }

    public function test_gemini_insights_try_the_configured_fallback_model(): void
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
                                            'text' => json_encode([
                                                'progress' => 'Perkembangan belajar sudah tercatat dan dapat ditingkatkan secara bertahap.',
                                                'schedule' => 'Gunakan jadwal belajar mingguan untuk menyelesaikan materi berikutnya.',
                                                'obstacles' => 'Kendala yang tercatat perlu ditinjau sebelum sesi belajar berikutnya.',
                                            ], JSON_THROW_ON_ERROR),
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

        $user = User::factory()->create([
            'weekly_study_hours' => 6,
        ]);

        $result = app(AiInsightService::class)
            ->progress(
                $user,
                [
                    'score' => 25,
                    'skill_mastery' => 30,
                    'roadmap_completion' => 10,
                    'project_score' => 0,
                    'consistency' => 20,
                    'evaluation_score' => 0,
                ],
            );

        $this->assertTrue(
            $result['generated_by_ai'],
        );

        $this->assertSame(
            'gemini-3.1-flash-lite',
            $result['model'],
        );

        Http::assertSentCount(2);
    }
}
