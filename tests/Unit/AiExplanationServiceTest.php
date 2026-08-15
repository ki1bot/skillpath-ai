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
    }

    public function test_no_explanation_is_returned_when_openrouter_is_not_configured(): void
    {
        config([
            'services.openrouter.key' => null,
            'services.openrouter.model' => 'openai/gpt-oss-20b:free',
            'services.openrouter.fallback_models' => [
                'openrouter/free',
            ],
            'services.openrouter.base_url' => 'https://openrouter.ai/api/v1',
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
            'services.openrouter.key' => 'test-openrouter-key',
            'services.openrouter.model' => 'openai/gpt-oss-20b:free',
            'services.openrouter.fallback_models' => [
                'openrouter/free',
            ],
            'services.openrouter.base_url' => 'https://openrouter.ai/api/v1',
        ]);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response(
                [
                    'id' => 'generation-test',
                    'model' => 'openai/gpt-oss-20b:free',
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
            'openai/gpt-oss-20b:free',
            $result->model,
        );

        $this->assertSame(
            'Database menjadi prioritas utama karena skor Anda masih 30 dari target 75. Kesenjangan ini perlu diprioritaskan sebelum kemampuan lanjutan yang bergantung pada pengelolaan data.',
            $result->summary,
        );

        Http::assertSent(
            fn ($request) => $request->url()
                === 'https://openrouter.ai/api/v1/chat/completions'
                && $request['model'] === 'openai/gpt-oss-20b:free'
                && ! isset($request['response_format']),
        );
    }

    public function test_invalid_ai_response_is_not_replaced_with_system_text(): void
    {
        config([
            'services.openrouter.key' => 'test-openrouter-key',
            'services.openrouter.model' => 'openai/gpt-oss-20b:free',
            'services.openrouter.fallback_models' => [],
            'services.openrouter.base_url' => 'https://openrouter.ai/api/v1',
        ]);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response(
                [
                    'id' => 'generation-test',
                    'model' => 'openai/gpt-oss-20b:free',
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
