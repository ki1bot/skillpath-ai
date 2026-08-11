<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdleTimeoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_authenticated_user_can_send_heartbeat(): void
    {
        config([
            'security.idle_timeout_minutes' => 10,
        ]);

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->withSession([
                'auth.last_activity' => now()
                    ->subMinutes(5)
                    ->timestamp,
            ])
            ->get(
                route(
                    'session.heartbeat',
                ),
            );

        $response->assertNoContent();

        $this->assertAuthenticatedAs(
            $user,
        );
    }

    public function test_authenticated_user_is_logged_out_after_idle_timeout(): void
    {
        config([
            'security.idle_timeout_minutes' => 10,
        ]);

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->withSession([
                'auth.last_activity' => now()
                    ->subMinutes(11)
                    ->timestamp,
            ])
            ->get(
                route(
                    'session.heartbeat',
                ),
            );

        $response->assertRedirect(
            route('login'),
        );

        $response->assertSessionHas(
            'status',
        );

        $this->assertGuest();
    }
}
