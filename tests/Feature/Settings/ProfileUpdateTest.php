<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    private const SUPER_ADMIN_EMAIL = 'super-admin@example.test';

    public function test_profile_page_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('profile.edit'));

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_super_admin_email_cannot_be_changed()
    {
        config()->set(
            'security.user_manager_email',
            self::SUPER_ADMIN_EMAIL,
        );

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => self::SUPER_ADMIN_EMAIL,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->patch(route('profile.update'), [
                'name' => 'Super Admin',
                'email' => 'changed@example.test',
            ]);

        $response->assertSessionHasErrors([
            'email' => 'Alamat email super admin tidak dapat diubah.',
        ]);

        $superAdmin->refresh();

        $this->assertSame(
            self::SUPER_ADMIN_EMAIL,
            $superAdmin->email,
        );
    }

    public function test_user_can_delete_their_account()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('profile.destroy'), [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_super_admin_cannot_delete_their_account()
    {
        config()->set(
            'security.user_manager_email',
            self::SUPER_ADMIN_EMAIL,
        );

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => self::SUPER_ADMIN_EMAIL,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this
            ->actingAs($superAdmin)
            ->delete(route('profile.destroy'), [
                'password' => 'password',
            ])
            ->assertForbidden();

        $this->assertNotNull(
            $superAdmin->fresh(),
        );
    }

    public function test_correct_password_must_be_provided_to_delete_account()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->delete(route('profile.destroy'), [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect(route('profile.edit'));

        $this->assertNotNull($user->fresh());
    }
}
