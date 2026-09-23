<?php

namespace Tests\Unit;

use App\Models\Career;
use App\Models\User;
use App\Services\AiExplanationService;
use App\Services\AiInsightService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JuanRouterIntegrationTest extends TestCase
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
            ],

            'services.ai.request_timeout' => 30,
            'services.ai.attempt_timeout' => 10,
            'services.ai.connect_timeout' => 5,
            'services.ai.failure_cache_seconds' => 10,

            'services.ai.health_cooldown_seconds' => 45,
            'services.ai.health_max_cooldown_seconds' => 300,
            'services.ai.health_state_seconds' => 600,

            'services.juanrouter.key' => 'test-juanrouter-key',
            'services.juanrouter.model' => 'gpt-5.6-luna',
            'services.juanrouter.fallback_models' => [],
            'services.juanrouter.base_url' => 'https://router.juan.web.id/v1',
            'services.juanrouter.reasoning_effort' => 'low',

            'services.openrouter.key' => null,
            'services.xkiro.key' => null,
            'services.gemini.key' => null,
        ]);

        Http::preventStrayRequests();
    }

    public function test_juan_router_can_generate_ai_insight(): void
    {
        Http::fake([
            'https://router.juan.web.id/v1/chat/completions' => Http::response(
                [
                    'id' => 'juanrouter-insight-test',
                    'object' => 'chat.completion',
                    'model' => 'gpt-5.6-luna',

                    'choices' => [
                        [
                            'index' => 0,

                            'message' => [
                                'role' => 'assistant',

                                'content' => json_encode(
                                    [
                                        'progress' => 'Perkembangan belajar pengguna menunjukkan kemampuan yang masih perlu ditingkatkan berdasarkan data yang tersedia.',

                                        'schedule' => 'Jadwal belajar dapat difokuskan pada materi berikutnya dengan pembagian waktu yang sesuai dengan waktu belajar mingguan.',

                                        'obstacles' => 'Kendala belajar yang tercatat dapat digunakan sebagai dasar untuk menyesuaikan proses belajar berikutnya.',
                                    ],
                                    JSON_UNESCAPED_UNICODE
                                        | JSON_UNESCAPED_SLASHES,
                                ),
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

        $result = app(
            AiInsightService::class,
        )->progress(
            $user,
            [
                'score' => 35,
                'skill_mastery' => 40,
                'roadmap_completion' => 20,
                'project_score' => 0,
                'consistency' => 30,
                'evaluation_score' => 0,
            ],
        );

        $this->assertTrue(
            $result['generated_by_ai'],
        );

        $this->assertSame(
            'gpt-5.6-luna',
            $result['model'],
        );

        $this->assertNotNull(
            $result['progress'],
        );

        $this->assertNotNull(
            $result['schedule'],
        );

        $this->assertNotNull(
            $result['obstacles'],
        );

        Http::assertSent(
            function ($request): bool {
                return $request->url()
                    === 'https://router.juan.web.id/v1/chat/completions'

                    && $request['model']
                        === 'gpt-5.6-luna'

                    && $request[
                        'reasoning_effort'
                    ] === 'low'

                    && $request[
                        'max_completion_tokens'
                    ] === 1024

                    && ! isset(
                        $request['temperature'],
                    )

                    && ! isset(
                        $request['max_tokens'],
                    )

                    && $request['stream']
                        === false;
            },
        );

        Http::assertSentCount(
            1,
        );
    }

    public function test_juan_router_can_generate_skill_gap_explanation(): void
    {
        Http::fake([
            'https://router.juan.web.id/v1/chat/completions' => Http::response(
                [
                    'id' => 'juanrouter-explanation-test',
                    'object' => 'chat.completion',
                    'model' => 'gpt-5.6-luna',

                    'choices' => [
                        [
                            'index' => 0,

                            'message' => [
                                'role' => 'assistant',

                                'content' => 'Database menjadi prioritas utama karena kemampuan pengguna saat ini masih berada di bawah target. Kesenjangan tersebut perlu dipelajari terlebih dahulu agar kemampuan dasar menjadi lebih kuat sebelum melanjutkan ke keterampilan berikutnya.',
                            ],

                            'finish_reason' => 'stop',
                        ],
                    ],
                ],
                200,
            ),
        ]);

        $user = User::factory()->create();

        $user->setRelation(
            'targetCareer',
            new Career([
                'name' => 'Backend Developer',
            ]),
        );

        $result = app(
            AiExplanationService::class,
        )->skillGapSummary(
            $user,
            [
                [
                    'name' => 'Database',
                    'current' => 30.0,
                    'target' => 75.0,
                    'gap' => 45.0,
                    'priority' => 54.0,
                    'status' => 'kesenjangan_tinggi',

                    'reason' => 'Database perlu diperkuat.',

                    'prerequisites' => [
                        [
                            'id' => 1,
                            'name' => 'Dasar Pemrograman',
                            'slug' => 'dasar-pemrograman',
                        ],
                    ],
                ],
            ],
        );

        $this->assertTrue(
            $result->generatedByAi,
        );

        $this->assertSame(
            'gpt-5.6-luna',
            $result->model,
        );

        $this->assertNotNull(
            $result->summary,
        );

        Http::assertSent(
            function ($request): bool {
                return $request->url()
                    === 'https://router.juan.web.id/v1/chat/completions'

                    && $request['model']
                        === 'gpt-5.6-luna'

                    && $request[
                        'reasoning_effort'
                    ] === 'low'

                    && $request[
                        'max_completion_tokens'
                    ] === 1024

                    && ! isset(
                        $request['temperature'],
                    )

                    && ! isset(
                        $request['max_tokens'],
                    )

                    && $request['stream']
                        === false;
            },
        );

        Http::assertSentCount(
            1,
        );
    }
}
