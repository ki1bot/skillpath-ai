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

        $cacheKey = 'skill-gap-explanation:v3:'
            .$user->id
            .':'
            .sha1(
                $baseUrl
                    .'|'
                    .$model
                    .'|'
                    .$cachePayload,
            );

        $failureCacheKey = $cacheKey
            .':unavailable';

        $cached = Cache::get(
            $cacheKey,
        );

        if (
            is_string($cached)
            && trim($cached) !== ''
            && $this->looksIndonesian($cached)
        ) {
            return $cached;
        }

        if (
            Cache::get(
                $failureCacheKey,
            ) === true
        ) {
            return $fallback;
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
                $this->markUnavailable(
                    $failureCacheKey,
                );

                return $fallback;
            }

            $response = Http::withToken(
                $key,
            )
                ->acceptJson()
                ->asJson()
                ->connectTimeout(1)
                ->timeout(2)
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
                                'content' => 'Anda adalah pendamping belajar SkillPath AI untuk pengguna Indonesia. Semua jawaban WAJIB menggunakan Bahasa Indonesia. Jangan menjawab menggunakan Bahasa Inggris, kecuali nama teknologi, framework, bahasa pemrograman, atau istilah teknis yang memang umum digunakan dalam bentuk aslinya. Jelaskan hanya data yang diberikan tanpa membuat skill, nilai, kesenjangan, atau roadmap baru. Gunakan Bahasa Indonesia yang alami, jelas, dan singkat. Jawaban harus berupa satu paragraf berisi 2 sampai 3 kalimat. Jangan gunakan Markdown, tabel, heading, daftar, bullet, simbol bintang, tanda pagar, garis vertikal, atau backtick. Jelaskan prioritas utama berdasarkan kemampuan saat ini, target, kesenjangan, dan prasyarat.',
                            ],
                            [
                                'role' => 'user',
                                'content' => 'Jawab hanya dalam Bahasa Indonesia.'
                                    ."\nTarget karier: "
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
                        'temperature' => 0.2,
                        'max_tokens' => 220,
                    ],
                );

            if (! $response->successful()) {
                $this->markUnavailable(
                    $failureCacheKey,
                );

                return $fallback;
            }

            $text = $response->json(
                'choices.0.message.content',
            );

            if (
                ! is_string($text)
                || trim($text) === ''
            ) {
                $this->markUnavailable(
                    $failureCacheKey,
                );

                return $fallback;
            }

            $text = $this->normalizeAiText(
                $text,
            );

            if ($text === null) {
                $this->markUnavailable(
                    $failureCacheKey,
                );

                return $fallback;
            }

            Cache::put(
                $cacheKey,
                $text,
                now()->addDay(),
            );

            Cache::forget(
                $failureCacheKey,
            );

            return $text;
        } catch (Throwable) {
            $this->markUnavailable(
                $failureCacheKey,
            );

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

        if (
            $normalized === ''
            || ! $this->looksIndonesian(
                $normalized,
            )
        ) {
            return null;
        }

        return Str::limit(
            $normalized,
            650,
            '',
        );
    }

    private function looksIndonesian(
        string $text,
    ): bool {
        $text = Str::lower(
            strip_tags($text),
        );

        $matches = [];

        $result = preg_match_all(
            '/\b(?:yang|dan|untuk|dengan|dari|pada|adalah|karena|anda|kamu|kemampuan|belajar|prioritas|utama|saat|masih|selisih|setelah|perlu|berikutnya|kesiapan|proyek|materi|kendala|waktu|target|nilai|penguatan)\b/u',
            $text,
            $matches,
        );

        return is_int($result)
            && $result >= 3;
    }

    private function markUnavailable(
        string $failureCacheKey,
    ): void {
        Cache::put(
            $failureCacheKey,
            true,
            now()->addMinutes(10),
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
