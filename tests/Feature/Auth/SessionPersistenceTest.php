<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_send_heartbeat(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('session.heartbeat'))
            ->assertNoContent();

        $this->assertAuthenticatedAs($user);
    }

    public function test_old_idle_activity_does_not_log_out_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession([
                'auth.last_activity' => now()
                    ->subDays(7)
                    ->timestamp,
            ])
            ->get(route('session.heartbeat'))
            ->assertNoContent();

        $this->assertAuthenticatedAs($user);
    }

    public function test_unverified_user_can_refresh_session(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->get(route('session.heartbeat'))
            ->assertNoContent();

        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_cannot_refresh_authenticated_session(): void
    {
        $this->get(route('session.heartbeat'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_session_cookie_expires_on_browser_close(): void
    {
        $this->assertTrue(
            (bool) config('session.expire_on_close'),
        );
    }
}
