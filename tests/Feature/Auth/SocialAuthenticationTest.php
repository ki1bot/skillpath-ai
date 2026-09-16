<?php

namespace Tests\Feature\Auth;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_social_account_can_authenticate(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        SocialAccount::query()->create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-user-1',
        ]);

        $this->mockProvider(
            'google',
            'google-user-1',
            null,
            null,
        );

        $response = $this
            ->withSession([
                'social-auth.intent' => 'authenticate',
                'social-auth.source' => 'login',
            ])
            ->get(
                route(
                    'social.callback',
                    [
                        'provider' => 'google',
                    ],
                ),
            );

        $response->assertRedirect(
            route('dashboard'),
        );

        $this->assertAuthenticatedAs(
            $user,
        );
    }

    public function test_matching_email_is_linked_to_existing_user(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.test',
            'email_verified_at' => null,
        ]);

        $this->mockProvider(
            'google',
            'google-user-2',
            'Existing User',
            'EXISTING@example.test',
        );

        $response = $this
            ->withSession([
                'social-auth.intent' => 'authenticate',
                'social-auth.source' => 'login',
            ])
            ->get(
                route(
                    'social.callback',
                    [
                        'provider' => 'google',
                    ],
                ),
            );

        $response->assertRedirect(
            route('dashboard'),
        );

        $this->assertAuthenticatedAs(
            $user,
        );

        $this->assertNotNull(
            $user
                ->fresh()
                ->email_verified_at,
        );

        $this->assertDatabaseHas(
            'social_accounts',
            [
                'user_id' => $user->id,
                'provider' => 'google',
                'provider_user_id' => 'google-user-2',
            ],
        );
    }

    public function test_facebook_can_create_and_authenticate_new_user(): void
    {
        $this->mockProvider(
            'facebook',
            'facebook-user-1',
            'Facebook User',
            'facebook@example.test',
        );

        $response = $this
            ->withSession([
                'social-auth.intent' => 'authenticate',
                'social-auth.source' => 'register',
            ])
            ->get(
                route(
                    'social.callback',
                    [
                        'provider' => 'facebook',
                    ],
                ),
            );

        $response->assertRedirect(
            route('dashboard'),
        );

        $user = User::query()
            ->where(
                'email',
                'facebook@example.test',
            )
            ->firstOrFail();

        $this->assertAuthenticatedAs(
            $user,
        );

        $this->assertSame(
            'Facebook User',
            $user->name,
        );

        $this->assertSame(
            'student',
            $user->role,
        );

        $this->assertNotNull(
            $user->email_verified_at,
        );

        $this->assertDatabaseHas(
            'social_accounts',
            [
                'user_id' => $user->id,
                'provider' => 'facebook',
                'provider_user_id' => 'facebook-user-1',
            ],
        );
    }

    public function test_social_authentication_without_email_is_rejected(): void
    {
        $this->mockProvider(
            'facebook',
            'facebook-user-2',
            'No Email User',
            null,
        );

        $response = $this
            ->withSession([
                'social-auth.intent' => 'authenticate',
                'social-auth.source' => 'login',
            ])
            ->get(
                route(
                    'social.callback',
                    [
                        'provider' => 'facebook',
                    ],
                ),
            );

        $response->assertRedirect(
            route('login'),
        );

        $response->assertSessionHasErrors([
            'social',
        ]);

        $this->assertGuest();

        $this->assertDatabaseMissing(
            'social_accounts',
            [
                'provider' => 'facebook',
                'provider_user_id' => 'facebook-user-2',
            ],
        );
    }

    private function mockProvider(
        string $provider,
        string $providerUserId,
        ?string $name,
        ?string $email,
    ): void {
        $providerUser = Mockery::mock(
            SocialiteUser::class,
        );

        $providerUser
            ->shouldReceive('getId')
            ->andReturn(
                $providerUserId,
            );

        if ($name !== null) {
            $providerUser
                ->shouldReceive('getName')
                ->andReturn(
                    $name,
                );
        }

        if ($email !== null) {
            $providerUser
                ->shouldReceive('getEmail')
                ->andReturn(
                    $email,
                );
        } else {
            $providerUser
                ->shouldReceive('getEmail')
                ->andReturnNull();
        }

        $driver = Mockery::mock();

        $driver
            ->shouldReceive('user')
            ->once()
            ->andReturn(
                $providerUser,
            );

        Socialite::shouldReceive('driver')
            ->once()
            ->with(
                $provider,
            )
            ->andReturn(
                $driver,
            );
    }
}
