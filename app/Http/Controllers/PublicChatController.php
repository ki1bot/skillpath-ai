<?php

namespace App\Http\Controllers;

use App\Services\Ai\PublicProjectChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicChatController extends Controller
{
    public function __invoke(
        Request $request,
        PublicProjectChatService $chat,
    ): JsonResponse {
        abort_if(
            $request->user() !== null,
            404,
        );

        abort_unless(
            (bool) config(
                'services.public_chat.enabled',
                true,
            ),
            404,
        );

        $validated = $request->validate([
            'message' => [
                'required',
                'string',
                'min:2',
                'max:600',
            ],

            'history' => [
                'sometimes',
                'array',
                'max:12',
            ],

            'history.*.role' => [
                'required',
                'string',
                Rule::in([
                    'user',
                    'assistant',
                ]),
            ],

            'history.*.content' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        /** @var array<int, array{role?: mixed, content?: mixed}> $history */
        $history = is_array(
            $validated['history'] ?? null,
        )
            ? $validated['history']
            : [];

        $result = $chat->reply(
            (string) $validated['message'],
            $history,
        );

        return response()->json(
            [
                'message' => $result['message'],
                'blocked' => $result['blocked'],
            ],
            $result['available']
                ? 200
                : 503,
        );
    }
}
