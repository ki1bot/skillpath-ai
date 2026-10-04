<?php

namespace Tests\Feature\Auth;

use App\Models\AuthIdentity;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;
use Tests\Concerns\UsesMongoCredentials;
use Tests\TestCase;

class MongoCredentialStoreTest extends TestCase
{
    use RefreshDatabase, UsesMongoCredentials;

    public function test_user_can_authenticate_using_mongodb_password_instead_of_postgresql_password(): void
    {
        $user = $this->createUserWithMongoPassword(
            'mongodb-password',
            'postgresql-password',
        );

        $response = $this->post(
            route('login.store'),
            [
                'email' => $user->email,
                'password' => 'mongodb-password',
            ],
        );

        $this->assertAuthenticatedAs(
            $user,
        );

        $response->assertRedirect(
            route(
                'dashboard',
                absolute: false,
            ),
        );

        $this->post(
            route('logout'),
        );

        $this->post(
            route('login.store'),
            [
                'email' => $user->email,
                'password' => 'postgresql-password',
            ],
        );

        $this->assertGuest();
    }

    public function test_registration_stores_login_password_in_mongodb(): void
    {
        $this->skipUnlessFortifyHas(
            Features::registration(),
        );

        Mail::fake();

        $response = $this->post(
            route('register.store'),
            [
                'name' => 'Mongo User',
                'email' => 'MONGO.USER@EXAMPLE.TEST',
                'password' => 'mongodb-password',
                'password_confirmation' => 'mongodb-password',
            ],
        );

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                route(
                    'email-verification.show',
                ),
            );

        $user = User::query()
            ->where(
                'email',
                'mongo.user@example.test',
            )
            ->firstOrFail();

        $identity = AuthIdentity::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->firstOrFail();

        $this->assertSame(
            'mongo.user@example.test',
            $identity->email,
        );

        $this->assertTrue(
            Hash::check(
                'mongodb-password',
                (string) $identity->password_hash,
            ),
        );

        $this->assertFalse(
            Hash::check(
                'mongodb-password',
                (string) $user->getRawOriginal(
                    'password',
                ),
            ),
        );
    }

    public function test_password_update_changes_mongodb_password_without_replacing_postgresql_decoy(): void
    {
        $user = $this->createUserWithMongoPassword(
            'old-mongodb-password',
            'postgresql-decoy',
        );

        $postgresPasswordHash = (string) $user->getRawOriginal(
            'password',
        );

        $response = $this
            ->actingAs($user)
            ->from(
                route(
                    'security.edit',
                ),
            )
            ->put(
                route(
                    'user-password.update',
                ),
                [
                    'current_password' => 'old-mongodb-password',
                    'password' => 'new-mongodb-password',
                    'password_confirmation' => 'new-mongodb-password',
                ],
            );

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                route(
                    'security.edit',
                ),
            );

        $identity = AuthIdentity::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->firstOrFail();

        $this->assertTrue(
            Hash::check(
                'new-mongodb-password',
                (string) $identity->password_hash,
            ),
        );

        $this->assertSame(
            $postgresPasswordHash,
            (string) $user
                ->refresh()
                ->getRawOriginal(
                    'password',
                ),
        );
    }

    public function test_password_reset_changes_mongodb_password(): void
    {
        $this->skipUnlessFortifyHas(
            Features::resetPasswords(),
        );

        Notification::fake();

        $user = $this->createUserWithMongoPassword(
            'old-mongodb-password',
            'postgresql-decoy',
        );

        $this->post(
            route('password.email'),
            [
                'email' => $user->email,
            ],
        );

        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function (
                ResetPassword $notification,
            ) use ($user): bool {
                $response = $this->post(
                    route('password.update'),
                    [
                        'token' => $notification->token,
                        'email' => $user->email,
                        'password' => 'reset-mongodb-password',
                        'password_confirmation' => 'reset-mongodb-password',
                    ],
                );

                $response
                    ->assertSessionHasNoErrors()
                    ->assertRedirect(
                        route('login'),
                    );

                return true;
            },
        );

        $identity = AuthIdentity::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->firstOrFail();

        $this->assertTrue(
            Hash::check(
                'reset-mongodb-password',
                (string) $identity->password_hash,
            ),
        );
    }

    public function test_profile_email_update_is_synchronized_to_mongodb(): void
    {
        $user = $this->createUserWithMongoPassword(
            'mongodb-password',
            'postgresql-decoy',
        );

        $response = $this
            ->actingAs($user)
            ->patch(
                route('profile.update'),
                [
                    'name' => 'Updated Mongo User',
                    'email' => 'UPDATED.MONGO@EXAMPLE.TEST',
                ],
            );

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                route('profile.edit'),
            );

        $identity = AuthIdentity::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->firstOrFail();

        $this->assertSame(
            'updated.mongo@example.test',
            $identity->email,
        );
    }

    public function test_deleting_account_removes_mongodb_credential(): void
    {
        $user = $this->createUserWithMongoPassword(
            'mongodb-password',
            'postgresql-decoy',
        );

        $response = $this
            ->actingAs($user)
            ->delete(
                route('profile.destroy'),
                [
                    'password' => 'mongodb-password',
                ],
            );

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                route('home'),
            );

        $this->assertNull(
            User::query()->find(
                $user->id,
            ),
        );

        $this->assertNull(
            AuthIdentity::query()
                ->where(
                    'user_id',
                    $user->id,
                )
                ->first(),
        );
    }

    private function createUserWithMongoPassword(
        string $mongoPassword,
        string $postgresPassword,
    ): User {
        $user = User::factory()->create([
            'password' => $postgresPassword,
        ]);

        AuthIdentity::query()->create([
            'user_id' => (int) $user->id,
            'email' => strtolower(
                trim(
                    (string) $user->email,
                ),
            ),
            'password_hash' => Hash::make(
                $mongoPassword,
            ),
        ]);

        return $user;
    }
}
