<?php

namespace Tests\Feature\Auth;

use App\Mail\EmailVerificationCodeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(
            Features::registration(),
        );
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(
            route('register'),
        );

        $response->assertOk();
    }

    public function test_new_users_must_verify_email_after_registration(): void
    {
        Mail::fake();

        $response = $this->post(
            route('register.store'),
            [
                'name' => 'Test User',
                'email' => 'TEST.USER@GMAIL.COM',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'admin',
            ],
        );

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                route(
                    'email-verification.show',
                ),
            )
            ->assertSessionHas(
                'status',
                'verification-code-sent',
            );

        $user = User::query()
            ->where(
                'email',
                'test.user@gmail.com',
            )
            ->firstOrFail();

        $this->assertNull(
            $user->email_verified_at,
        );

        $this->assertSame(
            'student',
            $user->role,
        );

        $this->assertAuthenticatedAs(
            $user,
        );

        Mail::assertSent(
            EmailVerificationCodeMail::class,
            fn (EmailVerificationCodeMail $mail): bool => $mail->hasTo(
                'test.user@gmail.com',
            ),
        );
    }

    public function test_registration_rejects_invalid_email_format(): void
    {
        $response = $this->post(
            route('register.store'),
            [
                'name' => 'Invalid Email User',
                'email' => 'bukan-email',
                'password' => 'password',
                'password_confirmation' => 'password',
            ],
        );

        $response->assertSessionHasErrors([
            'email' => 'Format alamat email tidak valid.',
        ]);

        $this->assertDatabaseMissing(
            'users',
            [
                'email' => 'bukan-email',
            ],
        );

        $this->assertGuest();
    }
}
