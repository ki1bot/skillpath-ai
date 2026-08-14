<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
            return 'Kemampuan inti Anda sudah memenuhi target untuk jalur karier ini. Fokus berikutnya adalah memperkuat kemampuan melalui proyek, latihan, dan evaluasi berkala.';
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

        $cacheKey = 'skill-gap-explanation:v2:'
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
                                'content' => 'Anda adalah pendamping belajar SkillPath AI. Jelaskan hanya data yang diberikan tanpa membuat skill, nilai, kesenjangan, atau roadmap baru. Gunakan Bahasa Indonesia yang alami, jelas, dan singkat. Jawaban harus berupa satu paragraf berisi 2 sampai 3 kalimat. Jangan gunakan Markdown, tabel, heading, daftar, bullet, simbol bintang, tanda pagar, garis vertikal, atau backtick. Jelaskan prioritas utama berdasarkan kemampuan saat ini, target, kesenjangan, dan prasyarat.',
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
                        'max_tokens' => 220,
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

            $text = $this->normalizeAiText(
                $text,
            );

            if ($text === null) {
                return $fallback;
            }

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

    private function normalizeAiText(
        string $text,
    ): ?string {
        $text = trim(
            $text,
        );

        if ($text === '') {
            return null;
        }

        if (
            preg_match(
                '/(\*\*|```|^\s*#{1,6}\s|^\s*[-*]\s|\|)/mu',
                $text,
            ) === 1
        ) {
            return null;
        }

        $normalized = preg_replace(
            '/\s+/u',
            ' ',
            $text,
        );

        if (! is_string($normalized)) {
            return null;
        }

        $normalized = trim(
            $normalized,
        );

        if ($normalized === '') {
            return null;
        }

        return Str::limit(
            $normalized,
            650,
            '',
        );
    }

    private function fallback(
        array $priorities,
    ): string {
        $first = $priorities[0];
        $second = $priorities[1] ?? null;
        $third = $priorities[2] ?? null;

        $summary = "Prioritas utama Anda adalah {$first['name']}. Kemampuan saat ini berada di {$first['current']} dari target {$first['target']}, sehingga masih terdapat selisih {$first['gap']} poin.";

        if ($second && $third) {
            $summary .= " Setelah itu, perkuat {$second['name']} dan {$third['name']} karena keduanya masih memiliki kesenjangan yang perlu ditutup sebelum melanjutkan ke kemampuan yang lebih lanjut.";
        } elseif ($second) {
            $summary .= " Setelah itu, perkuat {$second['name']} karena masih memiliki selisih {$second['gap']} poin dari target.";
        }

        return $summary;
    }
}
