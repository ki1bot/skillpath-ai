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

        Feedback::create([
            ...$validated,
            'user_id' => $request
                ->user()
                ->id,
            'status' => 'pending',
        ]);

        return back()->with(
            'success',
            'Masukan berhasil dikirim.',
        );
    }
}
