<?php

namespace Tests\Feature;

use App\Models\Career;
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

    public function test_admin_without_learning_profile_is_sent_to_onboarding(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('dashboard'));

        $response->assertRedirect(
            route('onboarding.show'),
        );
    }

    public function test_admin_with_learning_profile_can_visit_student_dashboard(): void
    {
        $career = Career::create([
            'name' => 'Sistem Informasi',
            'slug' => 'sistem-informasi',
            'tagline' => 'Menghubungkan data, proses bisnis, sistem, dan kebutuhan pengguna.',
            'description' => 'Jurusan Sistem Informasi untuk pengujian dashboard.',
            'responsibilities' => [
                'Analisis Data',
                'Pengembangan Sistem',
                'UI/UX',
            ],
            'difficulty' => 'Menengah',
            'accent' => '#79D7FF',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
            'study_program' => 'Sistem Informasi',
            'semester' => 5,
            'interest_area' => 'Pengembangan Sistem',
            'experience' => 'Administrator pengujian.',
            'weekly_study_hours' => 8,
            'target_career_id' => $career->id,
            'onboarding_completed_at' => now(),
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('dashboard')
                    ->has('career')
                    ->has('readiness')
                    ->has('priorities')
                    ->has('skillChart'),
            );
    }
}
