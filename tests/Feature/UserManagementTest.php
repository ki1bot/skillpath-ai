<?php

namespace Tests\Feature;

use App\Mail\AccountDeletedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private const MANAGER_EMAIL = 'user-manager@example.test';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set(
            'security.user_manager_email',
            self::MANAGER_EMAIL,
        );
    }

    public function test_verified_configured_admin_can_open_user_management_page(): void
    {
        $manager = $this->manager();

        User::factory()->create([
            'role' => 'student',
        ]);

        $this->actingAs($manager)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('admin/users')
                    ->where(
                        'managerId',
                        $manager->id,
                    )
                    ->has('users.data', 2),
            );
    }

    public function test_configured_manager_email_without_admin_role_cannot_manage_users(): void
    {
        $student = User::factory()->create([
            'name' => 'Manager Student',
            'email' => self::MANAGER_EMAIL,
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_unverified_configured_admin_cannot_manage_users(): void
    {
        $admin = User::factory()->create([
            'name' => 'Manager Belum Terverifikasi',
            'email' => self::MANAGER_EMAIL,
            'role' => 'admin',
            'email_verified_at' => null,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertRedirect(
                route('email-verification.show'),
            );

        $this->assertFalse(
            $admin->canManageUsers(),
        );
    }

    public function test_admin_using_rifqi_admin_name_cannot_open_user_management_page(): void
    {
        $admin = User::factory()->create([
            'name' => 'RifqiAdmin',
            'email' => 'another-admin@example.test',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_regular_admin_cannot_gain_user_manager_access_by_renaming_profile(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Biasa',
            'email' => 'regular-admin@example.test',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->patch(
                route('profile.update'),
                [
                    'name' => 'RifqiAdmin',
                    'email' => 'regular-admin@example.test',
                ],
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                route('profile.edit'),
            );

        $this->assertSame(
            'RifqiAdmin',
            $admin->fresh()->name,
        );

        $this->actingAs($admin->fresh())
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_user_management_fails_closed_when_manager_email_is_not_configured(): void
    {
        config()->set(
            'security.user_manager_email',
            null,
        );

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_manager_can_change_student_role_to_admin(): void
    {
        $manager = $this->manager();

        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $this->actingAs($manager)
            ->patch(
                route(
                    'admin.users.role.update',
                    $student,
                ),
                [
                    'role' => 'admin',
                ],
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(
            'admin',
            $student->fresh()->role,
        );
    }

    public function test_manager_can_change_admin_role_to_student(): void
    {
        $manager = $this->manager();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($manager)
            ->patch(
                route(
                    'admin.users.role.update',
                    $admin,
                ),
                [
                    'role' => 'student',
                ],
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(
            'student',
            $admin->fresh()->role,
        );
    }

    public function test_manager_cannot_change_own_role(): void
    {
        $manager = $this->manager();

        $this->actingAs($manager)
            ->patch(
                route(
                    'admin.users.role.update',
                    $manager,
                ),
                [
                    'role' => 'student',
                ],
            )
            ->assertSessionHasErrors([
                'role' => 'Role akun pengelola pengguna tidak dapat diubah dari halaman ini.',
            ]);

        $this->assertSame(
            'admin',
            $manager->fresh()->role,
        );
    }

    public function test_role_must_be_admin_or_student(): void
    {
        $manager = $this->manager();

        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $this->actingAs($manager)
            ->patch(
                route(
                    'admin.users.role.update',
                    $student,
                ),
                [
                    'role' => 'superadmin',
                ],
            )
            ->assertSessionHasErrors('role');

        $this->assertSame(
            'student',
            $student->fresh()->role,
        );
    }

    public function test_manager_can_delete_user_and_notification_email_is_sent(): void
    {
        Mail::fake();

        $manager = $this->manager();

        $user = User::factory()->create([
            'name' => 'Akun Dihapus',
            'email' => 'deleted-user@example.test',
            'role' => 'student',
        ]);

        $userId = $user->id;
        $userEmail = (string) $user->email;

        $this->actingAs($manager)
            ->delete(
                route(
                    'admin.users.destroy',
                    $user,
                ),
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseMissing(
            'users',
            [
                'id' => $userId,
            ],
        );

        Mail::assertSent(
            AccountDeletedMail::class,
            fn (AccountDeletedMail $mail): bool => $mail->hasTo(
                $userEmail,
            )
            && $mail->recipientEmail === $userEmail,
        );
    }

    public function test_manager_cannot_delete_own_account(): void
    {
        Mail::fake();

        $manager = $this->manager();

        $this->actingAs($manager)
            ->delete(
                route(
                    'admin.users.destroy',
                    $manager,
                ),
            )
            ->assertSessionHasErrors([
                'delete' => 'Akun pengelola pengguna tidak dapat dihapus dari halaman ini.',
            ]);

        $this->assertDatabaseHas(
            'users',
            [
                'id' => $manager->id,
                'email' => self::MANAGER_EMAIL,
            ],
        );

        Mail::assertNothingSent();
    }

    public function test_regular_admin_cannot_delete_user(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'name' => 'Admin Biasa',
            'email' => 'regular-admin@example.test',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $user = User::factory()->create([
            'email' => 'protected-user@example.test',
        ]);

        $this->actingAs($admin)
            ->delete(
                route(
                    'admin.users.destroy',
                    $user,
                ),
            )
            ->assertForbidden();

        $this->assertDatabaseHas(
            'users',
            [
                'id' => $user->id,
            ],
        );

        Mail::assertNothingSent();
    }

    private function manager(): User
    {
        return User::factory()->create([
            'name' => 'User Manager',
            'email' => self::MANAGER_EMAIL,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }
}
