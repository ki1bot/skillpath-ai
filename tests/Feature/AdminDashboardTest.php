<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_visit_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component(
                        'admin/dashboard',
                    )
                    ->has('stats')
                    ->has('overview'),
            );
    }

    public function test_admin_can_visit_management_page_separately(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.index'));

        $response
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component(
                        'admin/index',
                    ),
            );
    }

    public function test_student_cannot_visit_admin_dashboard(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($student)
            ->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_visiting_student_dashboard_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('dashboard'));

        $response->assertRedirect(
            route('admin.dashboard'),
        );
    }
}
