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
    ): ?string {
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
            return null;
        }

        $skills = collect(
            $analysis,
        )
            ->take(8)
            ->map(
                fn (array $item) => [
                    'skill' => $item['name'],
                    'current' => $item['current'],
                    'target' => $item['target'],
                    'gap' => $item['gap'],
                    'priority' => $item['priority'],
                    'status' => $item['status'],
                    'prerequisites' => collect(
                        $item['prerequisites'],
                    )
                        ->pluck('name')
                        ->all(),
                ],
            )
            ->values()
            ->all();

        $context = [
            'target_career' => $user
                ->targetCareer
                ?->name,
            'skills' => $skills,
        ];

        $contextJson = json_encode(
            $context,
            JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES,
        );

        if (! is_string($contextJson)) {
            return null;
        }

        $cacheKey = 'skill-gap-explanation:v4:'
            .$user->id
            .':'
            .sha1(
                $baseUrl
                    .'|'
                    .$model
                    .'|'
                    .$contextJson,
            );

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

        try {
            $response = Http::withToken(
                $key,
            )
                ->acceptJson()
                ->asJson()
                ->connectTimeout(2)
                ->timeout(8)
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
                                'content' => 'Anda adalah pendamping belajar SkillPath AI. Locale aplikasi adalah '.app()->getLocale().'. Seluruh jawaban yang ditampilkan kepada pengguna wajib menggunakan Bahasa Indonesia. Nama teknologi, framework, bahasa pemrograman, API, database, dan istilah teknis boleh tetap menggunakan nama aslinya. Gunakan hanya data yang diberikan. Jangan membuat skill, nilai, kesenjangan, fakta, atau roadmap baru. Jelaskan kondisi kemampuan pengguna, prioritas yang perlu ditingkatkan, hubungan dengan target, dan prasyarat yang relevan. Jika seluruh target telah terpenuhi, jelaskan kondisi tersebut berdasarkan data yang diberikan. Gunakan satu paragraf berisi 2 sampai 3 kalimat. Jangan gunakan Markdown, heading, tabel, bullet, tanda bintang, tanda pagar, garis vertikal, atau backtick.',
                            ],
                            [
                                'role' => 'user',
                                'content' => $contextJson,
                            ],
                        ],
                        'temperature' => 0.2,
                        'max_tokens' => 260,
                    ],
                );

            if (! $response->successful()) {
                return null;
            }

            $text = $response->json(
                'choices.0.message.content',
            );

            if (! is_string($text)) {
                return null;
            }

            $text = $this->normalizeAiText(
                $text,
            );

            if ($text === null) {
                return null;
            }

            Cache::put(
                $cacheKey,
                $text,
                now()->addHours(12),
            );

            return $text;
        } catch (Throwable) {
            return null;
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
                '/(\*\*|```|^\s*#{1,6}\s|\|)/mu',
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
            700,
            '',
        );
    }

    private function looksIndonesian(
        string $text,
    ): bool {
        $text = Str::lower(
            strip_tags($text),
        );

        $indonesianMatches = [];
        $englishMatches = [];

        $indonesianCount = preg_match_all(
            '/\b(?:yang|dan|untuk|dengan|dari|pada|adalah|karena|anda|kamu|kemampuan|belajar|prioritas|utama|saat|masih|selisih|setelah|perlu|berikutnya|kesiapan|proyek|materi|kendala|waktu|target|nilai|penguatan|risiko|langkah|evaluasi|jadwal|progres|perkembangan|berhasil|dibuat|kekuatan|sudah|ditingkatkan|hasil|pengguna|tercatat|memiliki|menjadi|berada|dapat|belum|lebih|sesuai|kesenjangan|karier|diprioritaskan|memenuhi|fondasi|lanjutan)\b/u',
            $text,
            $indonesianMatches,
        );

        $englishCount = preg_match_all(
            '/\b(?:the|and|for|with|from|this|that|your|you|is|are|was|were|to|of|in|on|because|after|before|current|learning|should|needs|need|next|improve|improved|based|using|use|has|have|still|result|results|successfully|created|development|strength|risk|step|steps|weekly|minutes|recorded|skill|skills)\b/u',
            $text,
            $englishMatches,
        );

        if (
            ! is_int($indonesianCount)
            || ! is_int($englishCount)
        ) {
            return false;
        }

        return $indonesianCount >= 2
            && $indonesianCount > $englishCount;
    }
}
