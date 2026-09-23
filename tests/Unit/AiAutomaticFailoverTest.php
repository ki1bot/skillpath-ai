<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AiInsightService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAutomaticFailoverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_failed_provider_is_skipped_automatically_on_next_request(): void
    {
        config([
            'services.ai.provider_order' => [
                'openrouter',
                'xkiro',
                'gemini',
            ],
            'services.ai.health_cooldown_seconds' => 45,
            'services.ai.health_max_cooldown_seconds' => 300,
            'services.ai.health_state_seconds' => 600,

            'services.openrouter.key' => 'test-openrouter-key',
            'services.openrouter.model' => 'openrouter-primary',
            'services.openrouter.fallback_models' => [],
            'services.openrouter.base_url' => 'https://openrouter.test/v1',

            'services.xkiro.key' => 'test-xkiro-key',
            'services.xkiro.model' => 'xkiro-primary',
            'services.xkiro.fallback_models' => [],
            'services.xkiro.base_url' => 'https://xkiro.test/v1',

            'services.gemini.key' => null,
        ]);

        $openrouterCalls = 0;
        $xkiroCalls = 0;

        Http::fake(
            function ($request) use (
                &$openrouterCalls,
                &$xkiroCalls,
            ) {
                if (
                    str_contains(
                        $request->url(),
                        'openrouter.test',
                    )
                ) {
                    $openrouterCalls++;

                    return Http::response(
                        [
                            'error' => [
                                'message' => 'Temporary provider error',
                            ],
                        ],
                        503,
                    );
                }

                if (
                    str_contains(
                        $request->url(),
                        'xkiro.test',
                    )
                ) {
                    $xkiroCalls++;

                    return Http::response(
                        [
                            'id' => 'xkiro-test',
                            'model' => 'xkiro-primary',
                            'choices' => [
                                [
                                    'index' => 0,
                                    'message' => [
                                        'role' => 'assistant',
                                        'content' => '<PROGRESS>Perkembangan belajar berhasil dianalisis.</PROGRESS>'
                                            .'<SCHEDULE>Jadwal belajar berhasil disusun berdasarkan data yang tersedia.</SCHEDULE>'
                                            .'<OBSTACLES>Kendala belajar yang tercatat berhasil dirangkum.</OBSTACLES>',
                                    ],
                                    'finish_reason' => 'stop',
                                ],
                            ],
                        ],
                        200,
                    );
                }

                return Http::response(
                    [],
                    500,
                );
            },
        );

        $readiness = [
            'score' => 25,
            'skill_mastery' => 30,
            'roadmap_completion' => 10,
            'project_score' => 0,
            'consistency' => 20,
            'evaluation_score' => 0,
        ];

        $firstUser = User::factory()->create([
            'weekly_study_hours' => 6,
        ]);

        $firstResult = app(
            AiInsightService::class,
        )->progress(
            $firstUser,
            $readiness,
        );

        $this->assertTrue(
            $firstResult['generated_by_ai'],
        );

        $this->assertSame(
            'xkiro-primary',
            $firstResult['model'],
        );

        $secondUser = User::factory()->create([
            'weekly_study_hours' => 6,
        ]);

        $secondResult = app(
            AiInsightService::class,
        )->progress(
            $secondUser,
            $readiness,
        );

        $this->assertTrue(
            $secondResult['generated_by_ai'],
        );

        $this->assertSame(
            'xkiro-primary',
            $secondResult['model'],
        );

        $this->assertSame(
            1,
            $openrouterCalls,
        );

        $this->assertSame(
            2,
            $xkiroCalls,
        );
    }
}
