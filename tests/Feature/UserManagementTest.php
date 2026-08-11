<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_email_can_open_user_management_page(): void
    {
        $owner = User::factory()->create([
            'name' => 'Nama Berbeda',
            'email' => 'f8goodspoof@gmail.com',
            'role' => 'student',
        ]);

        User::factory()->create([
            'role' => 'student',
        ]);

        $this->actingAs($owner)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('admin/users')
                    ->has('users.data', 2),
            );
    }

    public function test_rifqi_admin_with_admin_role_can_open_user_management_page(): void
    {
        $owner = User::factory()->create([
            'name' => 'RifqiAdmin',
            'email' => 'another@example.com',
            'role' => 'admin',
        ]);

        $this->actingAs($owner)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_student_using_rifqi_admin_name_cannot_open_user_management_page(): void
    {
        $student = User::factory()->create([
            'name' => 'RifqiAdmin',
            'email' => 'student@example.com',
            'role' => 'student',
        ]);

        $this->actingAs($student)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_regular_admin_cannot_open_user_management_page(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Biasa',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_owner_can_change_student_role_to_admin(): void
    {
        $owner = User::factory()->create([
            'name' => 'RifqiAdmin',
            'email' => 'f8goodspoof@gmail.com',
            'role' => 'admin',
        ]);

        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $this->actingAs($owner)
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

    public function test_owner_can_change_admin_role_to_student(): void
    {
        $owner = User::factory()->create([
            'name' => 'RifqiAdmin',
            'email' => 'f8goodspoof@gmail.com',
            'role' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($owner)
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

    public function test_role_must_be_admin_or_student(): void
    {
        $owner = User::factory()->create([
            'name' => 'RifqiAdmin',
            'email' => 'f8goodspoof@gmail.com',
            'role' => 'admin',
        ]);

        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $this->actingAs($owner)
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
}
