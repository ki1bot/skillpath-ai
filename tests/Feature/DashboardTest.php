<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_without_onboarding_are_redirected_to_onboarding(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertRedirect(route('onboarding.show'));
    }

    public function test_onboarded_authenticated_users_can_visit_the_dashboard(): void
    {
        $career = Career::create([
            'name' => 'Backend Developer',
            'slug' => 'backend-developer',
            'tagline' => 'Bangun layanan backend yang dapat diandalkan.',
            'description' => 'Jalur karier untuk pengembangan backend.',
            'responsibilities' => [
                'Merancang API',
                'Mengelola database',
            ],
            'difficulty' => 'Menengah',
            'accent' => '#C7FF5E',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'student',
            'study_program' => 'Sistem Informasi',
            'semester' => 5,
            'interest_area' => 'Backend',
            'experience' => 'Pernah membuat aplikasi web.',
            'weekly_study_hours' => 8,
            'target_career_id' => $career->id,
            'onboarding_completed_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
    }
}
