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

    public function test_unverified_user_can_open_optional_verification_page(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('email-verification.show'))
            ->assertOk();
    }

    public function test_verification_code_is_sent_only_to_authenticated_users_stored_email(): void
    {
        Mail::fake();

        $user = User::factory()->unverified()->create([
            'email' => 'account@example.com',
        ]);

        $this->actingAs($user)
            ->post(route('email-verification.send'), [
                'email' => 'other@example.com',
            ])
            ->assertRedirect(route('email-verification.show'));

        Mail::assertSent(
            EmailVerificationCodeMail::class,
            fn (EmailVerificationCodeMail $mail): bool => $mail->hasTo(
                'account@example.com',
            ),
        );
    }

    public function test_user_can_verify_email_with_valid_code(): void
    {
        Mail::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post(route('email-verification.send'))
            ->assertRedirect(route('email-verification.show'));

        $code = '';

        Mail::assertSent(
            EmailVerificationCodeMail::class,
            function (EmailVerificationCodeMail $mail) use (&$code, $user): bool {
                $code = $mail->code;

                return $mail->hasTo((string) $user->email);
            },
        );

        $this->actingAs($user)
            ->post(route('email-verification.verify'), [
                'code' => $code,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_invalid_verification_code_does_not_verify_email(): void
    {
        Mail::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post(route('email-verification.send'));

        $this->actingAs($user)
            ->post(route('email-verification.verify'), [
                'code' => '000000',
            ])
            ->assertSessionHasErrors('code');

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_email_verification_is_not_required_to_continue_using_the_application(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('onboarding.show'));
    }
}
