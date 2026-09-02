<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function index(
        Request $request,
    ): Response {
        return Inertia::render(
            'feedback',
            [
                'feedbacks' => Feedback::query()
                    ->where(
                        'user_id',
                        $request
                            ->user()
                            ->id,
                    )
                    ->latest()
                    ->get(),
            ],
        );
    }

    public function store(
        Request $request,
    ): RedirectResponse {
        $validated = $request->validate([
            'category' => [
                'required',
                'in:general,content,recommendation,usability,bug',
            ],
            'subject' => [
                'required',
                'string',
                'max:180',
            ],
            'message' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],
            'rating' => [
                'nullable',
                'integer',
                'min:1',
                'max:5',
            ],
        ]);

        $subject = trim(
            (string) $validated['subject'],
        );

        $message = trim(
            (string) $validated['message'],
        );

        $duplicateExists = Feedback::query()
            ->where(
                'user_id',
                $request
                    ->user()
                    ->id,
            )
            ->where(
                'category',
                $validated['category'],
            )
            ->where(
                'subject',
                $subject,
            )
            ->where(
                'message',
                $message,
            )
            ->where(
                'created_at',
                '>=',
                now()->subSeconds(30),
            )
            ->exists();

        if ($duplicateExists) {
            return back()->with(
                'error',
                'Masukan yang sama baru saja dikirim. Tidak perlu mengirimnya dua kali.',
            );
        }

        Feedback::create([
            'user_id' => $request
                ->user()
                ->id,
            'category' => $validated['category'],
            'subject' => $subject,
            'message' => $message,
            'rating' => isset($validated['rating'])
                ? (int) $validated['rating']
                : null,
            'status' => 'pending',
        ]);

        return back()->with(
            'success',
            'Masukan berhasil dikirim dan menunggu peninjauan administrator.',
        );
    }
}
