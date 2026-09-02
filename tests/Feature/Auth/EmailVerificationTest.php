<?php

namespace Tests\Feature\Auth;

use App\Mail\EmailVerificationCodeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_user_can_open_required_verification_page(): void
    {
        $user = User::factory()
            ->unverified()
            ->create();

        $this->actingAs(
            $user,
        )
            ->get(
                route(
                    'email-verification.show',
                ),
            )
            ->assertOk();
    }

    public function test_verification_code_is_sent_only_to_authenticated_users_stored_email(): void
    {
        Mail::fake();

        $user = User::factory()
            ->unverified()
            ->create([
                'email' => 'account@example.com',
            ]);

        $this->actingAs(
            $user,
        )
            ->post(
                route(
                    'email-verification.send',
                ),
                [
                    'email' => 'other@example.com',
                ],
            )
            ->assertRedirect(
                route(
                    'email-verification.show',
                ),
            );

        Mail::assertSent(
            EmailVerificationCodeMail::class,
            fn (EmailVerificationCodeMail $mail): bool => $mail->hasTo(
                'account@example.com',
            ),
        );
    }

    public function test_user_must_wait_five_minutes_before_requesting_new_verification_code(): void
    {
        Mail::fake();

        $user = User::factory()
            ->unverified()
            ->create();

        $this->actingAs($user)
            ->post(
                route('email-verification.send'),
            )
            ->assertRedirect(
                route('email-verification.show'),
            )
            ->assertSessionHas(
                'status',
                'verification-code-sent',
            );

        Mail::assertSent(
            EmailVerificationCodeMail::class,
            1,
        );

        $this->actingAs($user)
            ->post(
                route('email-verification.send'),
            )
            ->assertRedirect(
                route('email-verification.show'),
            )
            ->assertSessionHas(
                'status',
                'verification-code-cooldown',
            );

        Mail::assertSent(
            EmailVerificationCodeMail::class,
            1,
        );

        $this->travel(5)->minutes();

        $this->actingAs($user)
            ->post(
                route('email-verification.send'),
            )
            ->assertRedirect(
                route('email-verification.show'),
            )
            ->assertSessionHas(
                'status',
                'verification-code-sent',
            );

        Mail::assertSent(
            EmailVerificationCodeMail::class,
            2,
        );
    }

    public function test_user_can_verify_email_with_valid_code(): void
    {
        Mail::fake();

        $user = User::factory()
            ->unverified()
            ->create();

        $this->actingAs(
            $user,
        )
            ->post(
                route(
                    'email-verification.send',
                ),
            )
            ->assertRedirect(
                route(
                    'email-verification.show',
                ),
            );

        $code = '';

        Mail::assertSent(
            EmailVerificationCodeMail::class,
            function (
                EmailVerificationCodeMail $mail,
            ) use (
                &$code,
                $user,
            ): bool {
                $code = $mail->code;

                return $mail->hasTo(
                    (string) $user->email,
                );
            },
        );

        $this->actingAs(
            $user,
        )
            ->post(
                route(
                    'email-verification.verify',
                ),
                [
                    'code' => $code,
                ],
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                route(
                    'profile.edit',
                ),
            );

        $this->assertNotNull(
            $user
                ->fresh()
                ->email_verified_at,
        );
    }

    public function test_invalid_verification_code_does_not_verify_email(): void
    {
        Mail::fake();

        $user = User::factory()
            ->unverified()
            ->create();

        $this->actingAs(
            $user,
        )
            ->post(
                route(
                    'email-verification.send',
                ),
            );

        $this->actingAs(
            $user,
        )
            ->post(
                route(
                    'email-verification.verify',
                ),
                [
                    'code' => '000000',
                ],
            )
            ->assertSessionHasErrors(
                'code',
            );

        $this->assertNull(
            $user
                ->fresh()
                ->email_verified_at,
        );
    }

    public function test_unverified_user_cannot_use_main_application_features(): void
    {
        $user = User::factory()
            ->unverified()
            ->create();

        $this->actingAs(
            $user,
        )
            ->get(
                route(
                    'dashboard',
                ),
            )
            ->assertRedirect(
                route(
                    'email-verification.show',
                ),
            )
            ->assertSessionHas(
                'status',
                'verification-required',
            );
    }

    public function test_verified_user_is_not_blocked_by_email_verification(): void
    {
        $user = User::factory()
            ->create();

        $this->actingAs(
            $user,
        )
            ->get(
                route(
                    'dashboard',
                ),
            )
            ->assertRedirect(
                route(
                    'onboarding.show',
                ),
            );
    }
}
