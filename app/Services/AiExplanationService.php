<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        $configuredFallbackModels = config(
            'services.openrouter.fallback_models',
            [],
        );

        if (
            !is_string($key)
            || trim($key) === ''
            || !is_string($model)
            || trim($model) === ''
            || !is_string($baseUrl)
            || trim($baseUrl) === ''
        ) {
            return null;
        }

        $fallbackModels = [];

        if (is_array($configuredFallbackModels)) {
            foreach ($configuredFallbackModels as $fallbackModel) {
                if (
                    !is_string($fallbackModel)
                    || trim($fallbackModel) === ''
                    || trim($fallbackModel) === trim($model)
                ) {
                    continue;
                }

                $fallbackModels[] = trim(
                    $fallbackModel,
                );
            }
        }

        $fallbackModels = array_values(
            array_unique($fallbackModels),
        );

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

        if (!is_string($contextJson)) {
            return null;
        }

        $cacheKey = 'skill-gap-explanation:v5:'
            .$user->id
            .':'
            .sha1(
                $baseUrl
                    .'|'
                    .$model
                    .'|'
                    .implode(
                        '|',
                        $fallbackModels,
                    )
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

        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Anda adalah pendamping belajar SkillPath AI. Seluruh jawaban kepada pengguna wajib menggunakan Bahasa Indonesia. Nama teknologi, framework, bahasa pemrograman, API, database, library, dan istilah teknis yang lazim boleh tetap menggunakan nama aslinya. Gunakan hanya data yang diberikan. Jangan membuat skill, nilai, kesenjangan, fakta, kemampuan, atau roadmap baru. Analisis kondisi kemampuan pengguna, kemampuan yang paling perlu diprioritaskan, jarak terhadap target karier, dan prasyarat yang relevan. Hasil harus alami, spesifik terhadap data pengguna, ringkas, dan tidak mengandung Markdown.',
                ],
                [
                    'role' => 'user',
                    'content' => $contextJson,
                ],
            ],
            'temperature' => 0.2,
            'max_tokens' => 260,
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'skill_gap_explanation',
                    'strict' => true,
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'summary' => [
                                'type' => 'string',
                            ],
                        ],
                        'required' => [
                            'summary',
                        ],
                        'additionalProperties' => false,
                    ],
                ],
            ],
            'provider' => [
                'allow_fallbacks' => true,
            ],
        ];

        if ($fallbackModels !== []) {
            $payload['models'] = $fallbackModels;
        }

        try {
            $response = Http::withToken(
                $key,
            )
                ->withHeaders([
                    'HTTP-Referer' => (string) config(
                        'app.url',
                    ),
                    'X-Title' => (string) config(
                        'app.name',
                    ),
                ])
                ->acceptJson()
                ->asJson()
                ->connectTimeout(3)
                ->timeout(20)
                ->post(
                    rtrim(
                        $baseUrl,
                        '/',
                    ).'/chat/completions',
                    $payload,
                );

            if (!$response->successful()) {
                Log::warning(
                    'OpenRouter skill gap request failed.',
                    [
                        'status' => $response->status(),
                        'model' => $model,
                        'response' => Str::limit(
                            $response->body(),
                            500,
                            '',
                        ),
                    ],
                );

                return null;
            }

            $content = $response->json(
                'choices.0.message.content',
            );

            $summary = $this->extractSummary(
                $content,
            );

            if ($summary === null) {
                Log::warning(
                    'OpenRouter skill gap response was rejected.',
                    [
                        'model' => $response->json(
                            'model',
                        ),
                    ],
                );

                return null;
            }

            Cache::put(
                $cacheKey,
                $summary,
                now()->addHours(12),
            );

            return $summary;
        } catch (Throwable $exception) {
            Log::warning(
                'OpenRouter skill gap request threw an exception.',
                [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'model' => $model,
                ],
            );

            return null;
        }
    }

    private function extractSummary(
        mixed $content,
    ): ?string {
        if (!is_string($content)) {
            return null;
        }

        $content = trim(
            $content,
        );

        if ($content === '') {
            return null;
        }

        $content = preg_replace(
            '/^```(?:json)?\s*|\s*```$/iu',
            '',
            $content,
        );

        if (!is_string($content)) {
            return null;
        }

        $decoded = json_decode(
            $content,
            true,
        );

        if (
            !is_array($decoded)
            || !is_string(
                $decoded['summary'] ?? null,
            )
        ) {
            return null;
        }

        $summary = trim(
            $decoded['summary'],
        );

        $summary = preg_replace(
            '/\s+/u',
            ' ',
            $summary,
        );

        if (
            !is_string($summary)
            || $summary === ''
            || !$this->looksIndonesian(
                $summary,
            )
        ) {
            return null;
        }

        return Str::limit(
            $summary,
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
            '/\b(?:yang|dan|untuk|dengan|dari|pada|adalah|karena|anda|kamu|kemampuan|belajar|prioritas|utama|saat|masih|selisih|setelah|perlu|berikutnya|kesiapan|proyek|materi|kendala|waktu|target|nilai|penguatan|risiko|langkah|evaluasi|jadwal|progres|perkembangan|berhasil|dibuat|kekuatan|sudah|ditingkatkan|hasil|pengguna|tercatat|memiliki|menjadi|berada|dapat|belum|lebih|sesuai|kesenjangan|karier|diprioritaskan|memenuhi|fondasi|lanjutan|penguasaan|dasar|terhadap|sehingga|terutama|berfokus|meningkatkan)\b/u',
            $text,
            $indonesianMatches,
        );

        $englishCount = preg_match_all(
            '/\b(?:the|and|for|with|from|this|that|your|you|is|are|was|were|to|of|in|on|because|after|before|current|learning|should|needs|need|next|improve|improved|based|using|use|has|have|still|result|results|successfully|created|development|strength|risk|step|steps|weekly|minutes|recorded)\b/u',
            $text,
            $englishMatches,
        );

        if (
            !is_int($indonesianCount)
            || !is_int($englishCount)
        ) {
            return false;
        }

        return $indonesianCount >= 2
            && $indonesianCount > $englishCount;
    }
}
