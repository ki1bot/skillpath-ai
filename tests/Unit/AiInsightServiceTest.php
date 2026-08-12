<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AiInsightService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiInsightServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_insights_fall_back_safely_when_api_key_is_not_configured(): void
    {
        config([
            'services.openai.key' => null,
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
}
