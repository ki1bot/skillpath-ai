<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->latest()
            ->get();

        return Inertia::render('admin/feedback', [
            'feedbacks' => $feedbacks,
        ]);
    }

    public function update(Request $request, Feedback $feedback): RedirectResponse
    {
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

        $isPending = $validated['status'] === 'pending';

        $feedback->update([
            'status' => $validated['status'],
            'admin_response' => $validated['admin_response'] ?? null,
            'reviewed_by' => $isPending ? null : $request->user()->id,
            'reviewed_at' => $isPending ? null : now(),
        ]);

        return back()->with(
            'success',
            'Masukan pengguna berhasil diperbarui.',
        );
    }
}
