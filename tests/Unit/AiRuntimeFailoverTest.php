<?php

namespace Tests\Unit;

use App\Models\Career;
use App\Models\LearningMaterial;
use App\Models\Skill;
use App\Models\User;
use App\Services\AiExplanationService;
use App\Services\AiInsightService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiRuntimeFailoverTest extends TestCase
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
            'services.ai.failure_cache_seconds' => 10,
            'services.ai.health_cooldown_seconds' => 45,
            'services.ai.health_max_cooldown_seconds' => 300,
            'services.ai.health_state_seconds' => 600,

            'services.openrouter.key' => 'test-openrouter-key',
            'services.openrouter.model' => 'nex-agi/nex-n2.5-pro:free',
            'services.openrouter.fallback_models' => [
                'openrouter/free',
            ],
            'services.openrouter.base_url' => 'https://openrouter.test/v1',

            'services.gemini.key' => 'test-gemini-key',
            'services.gemini.model' => 'gemini-3.6-flash',
            'services.gemini.fallback_models' => [
                'gemini-3.5-flash-lite',
            ],
            'services.gemini.base_url' => 'https://gemini.test/v1beta',

            'services.juanrouter_backup.key' => 'test-backup-key',
            'services.juanrouter_backup.model' => 'deepseek-v4.1-flash',
            'services.juanrouter_backup.base_url' => 'https://backup.test/v1',

            'services.xkiro.key' => null,
            'services.juanrouter.key' => null,
        ]);

        Http::preventStrayRequests();
    }

    public function test_material_retry_can_use_backup_immediately_and_primary_returns_after_cooldown(): void
    {
        $phase = 1;
        $backupCalls = 0;

        Http::fake(
            function ($request) use (
                &$phase,
                &$backupCalls,
            ) {
                if (
                    $request->url()
                    === 'https://openrouter.test/v1/chat/completions'
                ) {
                    if (
                        $phase === 3
                        && $request['model']
                            === 'nex-agi/nex-n2.5-pro:free'
                    ) {
                        return Http::response(
                            [
                                'model' => 'nex-agi/nex-n2.5-pro:free',
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
                    }

                    return Http::response(
                        [
                            'error' => [
                                'message' => 'Provider unavailable',
                            ],
                        ],
                        503,
                    );
                }

                if (
                    $request->url()
                    === 'https://backup.test/v1/chat/completions'
                ) {
                    $backupCalls++;

                    if ($phase === 1) {
                        return Http::response(
                            [
                                'error' => [
                                    'message' => 'Backup temporarily unavailable',
                                ],
                            ],
                            503,
                        );
                    }

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

        $user = new User;
        $user->id = 7;

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

        $first = app(AiInsightService::class)
            ->exerciseVariation(
                $user,
                $material,
            );

        $this->assertFalse(
            $first['generated_by_ai'],
        );
        $this->assertNull($first['model']);
        $this->assertSame(1, $backupCalls);

        $phase = 2;

        $second = app(AiInsightService::class)
            ->exerciseVariation(
                $user,
                $material,
            );

        $this->assertTrue(
            $second['generated_by_ai'],
        );
        $this->assertSame(
            'deepseek-v4.1-flash',
            $second['model'],
        );
        $this->assertSame(2, $backupCalls);

        $phase = 3;

        $this->travel(91)->seconds();

        $third = app(AiInsightService::class)
            ->exerciseVariation(
                $user,
                $material,
            );

        $this->assertTrue(
            $third['generated_by_ai'],
        );
        $this->assertSame(
            'nex-agi/nex-n2.5-pro:free',
            $third['model'],
        );
        $this->assertSame(2, $backupCalls);
    }

    public function test_skill_retry_can_use_backup_even_while_previous_failures_are_cooling_down(): void
    {
        $backupCalls = 0;

        Http::fake(
            function ($request) use (&$backupCalls) {
                if (
                    str_starts_with(
                        $request->url(),
                        'https://gemini.test/v1beta/models/',
                    )
                ) {
                    return Http::response(
                        [
                            'error' => [
                                'message' => 'Gemini unavailable',
                            ],
                        ],
                        503,
                    );
                }

                if (
                    $request->url()
                    === 'https://backup.test/v1/chat/completions'
                ) {
                    $backupCalls++;

                    if ($backupCalls === 1) {
                        return Http::response(
                            [
                                'error' => [
                                    'message' => 'Backup temporarily unavailable',
                                ],
                            ],
                            503,
                        );
                    }

                    return Http::response(
                        [
                            'model' => 'deepseek-v4.1-flash',
                            'choices' => [
                                [
                                    'message' => [
                                        'role' => 'assistant',
                                        'content' => 'Prioritaskan normalisasi database, indexing, query plan, dan transaction isolation.',
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

        $user = new User;
        $user->id = 10;
        $user->setRelation(
            'targetCareer',
            new Career([
                'name' => 'Backend Developer',
            ]),
        );

        $analysis = [
            [
                'name' => 'Database',
                'current' => 30.0,
                'target' => 75.0,
                'gap' => 45.0,
                'priority' => 54.0,
                'status' => 'kesenjangan_tinggi',
                'prerequisites' => [],
            ],
        ];

        $first = app(AiExplanationService::class)
            ->skillGapSummary(
                $user,
                $analysis,
            );

        $this->assertFalse(
            $first->generatedByAi,
        );
        $this->assertNull($first->model);
        $this->assertSame(1, $backupCalls);

        $second = app(AiExplanationService::class)
            ->skillGapSummary(
                $user,
                $analysis,
            );

        $this->assertTrue(
            $second->generatedByAi,
        );
        $this->assertSame(
            'deepseek-v4.1-flash',
            $second->model,
        );
        $this->assertSame(2, $backupCalls);
    }
}
