<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class AiExplanationService
{
    public function skillGapSummary(
        User $user,
        array $analysis,
    ): string {
        $priorities = collect(
            $analysis,
        )
            ->filter(
                fn (array $item) => $item['gap'] > 0,
            )
            ->take(4)
            ->values();

        if ($priorities->isEmpty()) {
            return 'Kemampuan inti Anda sudah memenuhi target internal untuk jalur karier ini. Fokus berikutnya adalah memperkuat bukti melalui proyek dan evaluasi.';
        }

        $fallback = $this->fallback(
            $priorities->all(),
        );

        $key = config(
            'services.openrouter.key',
        );

        $model = config(
            'services.openrouter.model',
            'openrouter/free',
        );

        $baseUrl = config(
            'services.openrouter.base_url',
            'https://openrouter.ai/api/v1',
        );

        if (
            ! is_string($key)
            || trim($key) === ''
            || ! is_string($model)
            || trim($model) === ''
            || ! is_string($baseUrl)
            || trim($baseUrl) === ''
        ) {
            return $fallback;
        }

        $cachePayload = json_encode(
            $priorities->all(),
            JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES,
        );

        if (! is_string($cachePayload)) {
            return $fallback;
        }

        $cacheKey = 'skill-gap-explanation:'
            .$user->id
            .':'
            .sha1(
                $baseUrl
                    .'|'
                    .$model
                    .'|'
                    .$cachePayload,
            );

        $cached = Cache::get(
            $cacheKey,
        );

        if (
            is_string($cached)
            && trim($cached) !== ''
        ) {
            return $cached;
        }

        try {
            $payload = $priorities
                ->map(
                    fn (array $item) => [
                        'skill' => $item[
                            'name'
                        ],
                        'current' => $item[
                            'current'
                        ],
                        'target' => $item[
                            'target'
                        ],
                        'gap' => $item[
                            'gap'
                        ],
                        'priority' => $item[
                            'priority'
                        ],
                        'prerequisites' => collect(
                            $item[
                                'prerequisites'
                            ],
                        )
                            ->pluck('name')
                            ->all(),
                    ],
                )
                ->all();

            $payloadJson = json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES,
            );

            if (! is_string($payloadJson)) {
                return $fallback;
            }

            $response = Http::withToken(
                $key,
            )
                ->acceptJson()
                ->asJson()
                ->connectTimeout(3)
                ->timeout(20)
                ->post(
                    rtrim(
                        $baseUrl,
                        '/',
                    ).'/chat/completions',
                    [
                        'model' => $model,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'Anda adalah pendamping belajar SkillPath AI. Jelaskan data yang diberikan saja. Jangan membuat skill, nilai, atau roadmap baru. Gunakan Bahasa Indonesia yang ringkas, konkret, dan tidak berlebihan.',
                            ],
                            [
                                'role' => 'user',
                                'content' => 'Target karier: '
                                    .(
                                        $user
                                            ->targetCareer
                                            ?->name
                                        ?? '-'
                                    )
                                    ."\nData prioritas: "
                                    .$payloadJson,
                            ],
                        ],
                        'max_tokens' => 260,
                    ],
                );

            if (! $response->successful()) {
                return $fallback;
            }

            $text = $response->json(
                'choices.0.message.content',
            );

            if (
                ! is_string($text)
                || trim($text) === ''
            ) {
                return $fallback;
            }

            $text = trim(
                $text,
            );

            Cache::put(
                $cacheKey,
                $text,
                now()->addDay(),
            );

            return $text;
        } catch (Throwable) {
            return $fallback;
        }
    }

    private function fallback(
        array $priorities,
    ): string {
        $first = $priorities[0];
        $second = $priorities[1] ?? null;

        $summary = "Prioritas utama Anda adalah {$first['name']} karena masih memiliki gap {$first['gap']} poin dari target {$first['target']}.";

        if ($second) {
            $summary .= " Setelah itu, fokuskan {$second['name']} dengan gap {$second['gap']} poin.";
        }

        $summary .= ' Urutan belajar tetap mengikuti hubungan prasyarat agar materi lanjutan tidak dipelajari terlalu cepat.';

        return $summary;
    }
}
