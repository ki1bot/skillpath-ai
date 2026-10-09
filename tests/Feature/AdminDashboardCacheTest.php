<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminWorkspaceController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminDashboardCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_opening_dashboard_does_not_invalidate_existing_cache(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        Cache::put(
            AdminWorkspaceController::CACHE_KEY,
            ['cached' => true],
            now()->addMinutes(10),
        );

        $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->assertTrue(
            Cache::has(AdminWorkspaceController::CACHE_KEY),
        );
    }

    public function test_admin_write_request_invalidates_dashboard_cache(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        Cache::put(
            AdminWorkspaceController::CACHE_KEY,
            ['cached' => true],
            now()->addMinutes(10),
        );

        $this->assertTrue(
            Cache::has(AdminWorkspaceController::CACHE_KEY),
        );

        $this
            ->actingAs($admin)
            ->post(route('admin.skills.store'), [])
            ->assertSessionHasErrors();

        $this->assertFalse(
            Cache::has(AdminWorkspaceController::CACHE_KEY),
        );
    }

    public function test_student_cannot_access_cached_admin_dashboard(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        Cache::put(
            AdminWorkspaceController::CACHE_KEY,
            ['cached' => true],
            now()->addMinutes(10),
        );

        $this
            ->actingAs($student)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
