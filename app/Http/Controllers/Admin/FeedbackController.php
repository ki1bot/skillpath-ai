<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function index(): Response
    {
        $feedbacks = Feedback::query()
            ->with([
                'user:id,name,email',
                'reviewer:id,name,email',
            ])
            ->orderByRaw(
                "CASE status
                    WHEN 'pending' THEN 0
                    WHEN 'reviewing' THEN 1
                    WHEN 'resolved' THEN 2
                    ELSE 3
                END",
            )
            ->latest()
            ->get();

        return Inertia::render('admin/feedback', [
            'feedbacks' => $feedbacks,
        ]);
    }

    public function update(
        Request $request,
        Feedback $feedback,
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                'in:pending,reviewing,resolved',
            ],
            'admin_response' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ]);

        $adminResponse = trim(
            (string) (
                $validated['admin_response']
                ?? ''
            ),
        );

        if (
            $validated['status'] === 'resolved'
            && mb_strlen($adminResponse) < 10
        ) {
            throw ValidationException::withMessages([
                'admin_response' => 'Masukan yang diselesaikan harus memiliki tanggapan administrator minimal 10 karakter.',
            ]);
        }

        $isPending = (
            $validated['status']
            === 'pending'
        );

        $feedback->update([
            'status' => $validated['status'],
            'admin_response' => (
                $isPending
                || $adminResponse === ''
            )
                ? null
                : $adminResponse,
            'reviewed_by' => $isPending
                ? null
                : $request
                    ->user()
                    ->id,
            'reviewed_at' => $isPending
                ? null
                : now(),
        ]);

        return back()->with(
            'success',
            $validated['status'] === 'resolved'
                ? 'Masukan ditandai selesai dan tanggapan administrator telah disimpan.'
                : 'Status masukan berhasil diperbarui.',
        );
    }
}
