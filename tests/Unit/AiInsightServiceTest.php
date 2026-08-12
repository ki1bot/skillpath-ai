<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AiInsightService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiInsightServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_insights_fall_back_safely_when_api_key_is_not_configured(): void
    {
        config([
            'services.openrouter.key' => null,
            'services.openrouter.model' => 'openrouter/free',
            'services.openrouter.base_url' => 'https://openrouter.ai/api/v1',
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

        $this->assertNotSame(
            '',
            trim($result['progress']),
        );

        $this->assertNotSame(
            '',
            trim($result['schedule']),
        );

        $this->assertNotSame(
            '',
            trim($result['obstacles']),
        );
    }

    public function test_ai_insights_can_use_openrouter_chat_completion_response(): void
    {
        config([
            'services.openrouter.key' => 'test-openrouter-key',
            'services.openrouter.model' => 'openrouter/free',
            'services.openrouter.base_url' => 'https://openrouter.ai/api/v1',
        ]);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response(
                [
                    'id' => 'generation-test',
                    'model' => 'openai/gpt-oss-120b:free',
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
            'openai/gpt-oss-120b:free',
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
                && $request['model'] === 'openrouter/free',
        );
    }
}
