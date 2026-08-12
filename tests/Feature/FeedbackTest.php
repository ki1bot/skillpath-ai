<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_feedback_page(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        Feedback::create([
            'user_id' => $user->id,
            'category' => 'general',
            'subject' => 'Masukan pengujian',
            'message' => 'Masukan ini digunakan untuk menguji halaman feedback.',
            'rating' => 5,
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(
                route('feedback.index'),
            );

        $response->assertOk();
    }

    public function test_authenticated_user_can_submit_feedback(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(
                route('feedback.store'),
                [
                    'category' => 'bug',
                    'subject' => 'Pengujian feedback',
                    'message' => 'Terdapat masalah yang ingin saya laporkan melalui pengujian ini.',
                    'rating' => 4,
                ],
            );

        $response->assertRedirect();

        $this->assertDatabaseHas(
            'feedbacks',
            [
                'user_id' => $user->id,
                'category' => 'bug',
                'subject' => 'Pengujian feedback',
                'status' => 'pending',
                'rating' => 4,
            ],
        );
    }

    public function test_admin_can_open_feedback_management_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $student = User::factory()->create([
            'role' => 'student',
        ]);

        Feedback::create([
            'user_id' => $student->id,
            'category' => 'usability',
            'subject' => 'Masukan halaman',
            'message' => 'Tampilan halaman ini perlu diuji oleh administrator.',
            'rating' => 4,
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(
                route('admin.feedback.index'),
            );

        $response->assertOk();
    }

    public function test_student_cannot_open_admin_feedback_page(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $response = $this
            ->actingAs($student)
            ->get(
                route('admin.feedback.index'),
            );

        $response->assertForbidden();
    }

    public function test_admin_can_review_feedback(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $feedback = Feedback::create([
            'user_id' => $student->id,
            'category' => 'content',
            'subject' => 'Masukan materi',
            'message' => 'Materi pembelajaran perlu mendapatkan penjelasan tambahan.',
            'rating' => 4,
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch(
                route(
                    'admin.feedback.update',
                    $feedback,
                ),
                [
                    'status' => 'resolved',
                    'admin_response' => 'Masukan telah ditinjau dan ditindaklanjuti.',
                ],
            );

        $response->assertRedirect();

        $feedback->refresh();

        $this->assertSame(
            'resolved',
            $feedback->status,
        );

        $this->assertSame(
            'Masukan telah ditinjau dan ditindaklanjuti.',
            $feedback->admin_response,
        );

        $this->assertSame(
            $admin->id,
            $feedback->reviewed_by,
        );

        $this->assertNotNull(
            $feedback->reviewed_at,
        );
    }
}
