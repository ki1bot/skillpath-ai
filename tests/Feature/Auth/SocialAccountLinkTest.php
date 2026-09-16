<?php

namespace Tests\Feature\Auth;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialAccountLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_link_google_account(): void
    {
        $user = User::factory()->create();

        $this->mockSocialiteUser(
            'google',
            'google-user-123',
        );

        $response = $this
            ->actingAs($user)
            ->withSession([
                'social-auth.intent' => 'link',
                'social-auth.link_user_id' => $user->id,
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
            route('profile.edit'),
        );

        $this->assertAuthenticatedAs(
            $user,
        );

        $this->assertDatabaseHas(
            'social_accounts',
            [
                'user_id' => $user->id,
                'provider' => 'google',
                'provider_user_id' => 'google-user-123',
            ],
        );
    }

    public function test_provider_account_cannot_be_linked_to_another_user(): void
    {
        $owner = User::factory()->create();

        $user = User::factory()->create();

        SocialAccount::query()->create([
            'user_id' => $owner->id,
            'provider' => 'facebook',
            'provider_user_id' => 'facebook-user-123',
        ]);

        $this->mockSocialiteUser(
            'facebook',
            'facebook-user-123',
        );

        $response = $this
            ->actingAs($user)
            ->withSession([
                'social-auth.intent' => 'link',
                'social-auth.link_user_id' => $user->id,
            ])
            ->get(
                route(
                    'social.callback',
                    [
                        'provider' => 'facebook',
                    ],
                ),
            );

        $response
            ->assertRedirect(
                route('profile.edit'),
            )
            ->assertSessionHasErrors(
                'social',
            );

        $this->assertDatabaseMissing(
            'social_accounts',
            [
                'user_id' => $user->id,
                'provider' => 'facebook',
            ],
        );

        $this->assertDatabaseHas(
            'social_accounts',
            [
                'user_id' => $owner->id,
                'provider' => 'facebook',
                'provider_user_id' => 'facebook-user-123',
            ],
        );
    }

    public function test_profile_exposes_social_connection_status(): void
    {
        $user = User::factory()->create();

        SocialAccount::query()->create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-user-456',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(
                route('profile.edit'),
            );

        $response
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component(
                        'settings/profile',
                    )
                    ->where(
                        'socialConnections.google',
                        true,
                    )
                    ->where(
                        'socialConnections.facebook',
                        false,
                    ),
            );
    }

    private function mockSocialiteUser(
        string $provider,
        string $providerUserId,
    ): void {
        $providerUser = Mockery::mock(
            SocialiteUser::class,
        );

        $providerUser
            ->shouldReceive(
                'getId',
            )
            ->once()
            ->andReturn(
                $providerUserId,
            );

        $providerDriver = Mockery::mock(
            Provider::class,
        );

        $providerDriver
            ->shouldReceive(
                'user',
            )
            ->once()
            ->andReturn(
                $providerUser,
            );

        Socialite::shouldReceive(
            'driver',
        )
            ->once()
            ->with(
                $provider,
            )
            ->andReturn(
                $providerDriver,
            );
    }
}
