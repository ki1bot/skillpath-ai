<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(Features::registration());
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_must_login_after_registration(): void
    {
        Mail::fake();
        Notification::fake();

        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'TEST.USER@EXAMPLE.COM',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'))
            ->assertSessionHas(
                'status',
                'Akun berhasil dibuat. Silakan masuk menggunakan email dan kata sandi Anda.',
            );

        $this->assertGuest();

        $user = User::query()
            ->where('email', 'test.user@example.com')
            ->firstOrFail();

        $this->assertNull($user->email_verified_at);

        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }
}
