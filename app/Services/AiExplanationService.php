<?php

namespace App\Services;

use App\Models\User;
use App\Support\AiExplanationResult;
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
    ): AiExplanationResult {
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
                    'reason' => $item['reason'],
                    'prerequisites' => collect(
                        $item['prerequisites'],
                    )
                        ->pluck('name')
                        ->all(),
                ],
            )
            ->values()
            ->all();

        $careerName = $user
            ->targetCareer
            ?->name;

        if (
            ! is_string($careerName)
            || trim($careerName) === ''
        ) {
            $careerName = 'target karier Anda';
        }

        $priorities = [];

        foreach ($skills as $skill) {
            if ((float) $skill['gap'] <= 0) {
                continue;
            }

            $priorities[] = $skill;

            if (count($priorities) >= 3) {
                break;
            }
        }

        if ($priorities === []) {
            $fallbackSummary = 'Berdasarkan hasil yang tersimpan, keterampilan yang dianalisis untuk '
                .$careerName
                .' sudah memenuhi target profesi. Lanjutkan roadmap dan evaluasi berikutnya agar sistem dapat memperbarui kesiapan berdasarkan bukti progres terbaru.';
        } else {
            $first = $priorities[0];

            $firstReason = is_string(
                $first['reason'] ?? null,
            )
                ? trim($first['reason'])
                : '';

            if ($firstReason === '') {
                $firstReason = (string) $first['skill']
                    .' menjadi prioritas utama karena skor Anda '
                    .$first['current']
                    .' dari target '
                    .$first['target']
                    .', sehingga masih ada kesenjangan '
                    .$first['gap']
                    .' poin.';
            }

            $fallbackSummary = $firstReason;

            if (count($priorities) > 1) {
                $nextNames = [];

                foreach (array_slice($priorities, 1) as $priority) {
                    $name = $priority['skill'] ?? null;

                    if (
                        is_string($name)
                        && trim($name) !== ''
                    ) {
                        $nextNames[] = trim($name);
                    }
                }

                if ($nextNames !== []) {
                    $fallbackSummary .= ' Setelah itu, fokus berikutnya adalah '
                        .implode(' dan ', $nextNames)
                        .'.';
                }
            }

            $fallbackSummary .= ' Urutan ini mengikuti skor kesenjangan, bobot kepentingan, dan hubungan prasyarat yang sudah dihitung oleh SkillPath AI.';
        }

        $fallback = new AiExplanationResult(
            Str::limit(
                $fallbackSummary,
                700,
                '',
            ),
            false,
        );

        $key = config(
            'services.openrouter.key',
        );

        $model = config(
            'services.openrouter.model',
            'openai/gpt-oss-20b:free',
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
            ! is_string($key)
            || trim($key) === ''
            || ! is_string($model)
            || trim($model) === ''
            || ! is_string($baseUrl)
            || trim($baseUrl) === ''
        ) {
            return $fallback;
        }

        $models = [
            trim($model),
        ];

        if (is_array($configuredFallbackModels)) {
            foreach ($configuredFallbackModels as $fallbackModel) {
                if (
                    ! is_string($fallbackModel)
                    || trim($fallbackModel) === ''
                ) {
                    continue;
                }

                $models[] = trim($fallbackModel);
            }
        }

        $models = array_values(
            array_unique($models),
        );

        $context = [
            'target_career' => $careerName,
            'skills' => $skills,
        ];

        $contextJson = json_encode(
            $context,
            JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES,
        );

        if (! is_string($contextJson)) {
            return $fallback;
        }

        $cacheKey = 'skill-gap-explanation:v6:'
            .$user->id
            .':'
            .sha1(
                $baseUrl
                    .'|'
                    .implode('|', $models)
                    .'|'
                    .$contextJson,
            );

        $cached = Cache::get(
            $cacheKey,
        );

        if (
            is_array($cached)
            && is_string(
                $cached['summary'] ?? null,
            )
            && is_bool(
                $cached['generated_by_ai'] ?? null,
            )
            && $this->looksIndonesian(
                $cached['summary'],
            )
        ) {
            $cachedModel = $cached['model'] ?? null;

            if (
                $cached['generated_by_ai']
                && ! is_string($cachedModel)
            ) {
                $cachedModel = null;
            }

            return new AiExplanationResult(
                $cached['summary'],
                $cached['generated_by_ai'],
                is_string($cachedModel)
                    ? $cachedModel
                    : null,
            );
        }

        foreach ($models as $candidateModel) {
            $result = $this->requestSummary(
                $key,
                $baseUrl,
                $candidateModel,
                $contextJson,
            );

            if ($result === null) {
                continue;
            }

            Cache::put(
                $cacheKey,
                [
                    'summary' => $result->summary,
                    'model' => $result->model,
                    'generated_by_ai' => true,
                ],
                now()->addHours(12),
            );

            return $result;
        }

        Cache::put(
            $cacheKey,
            [
                'summary' => $fallback->summary,
                'model' => null,
                'generated_by_ai' => false,
            ],
            now()->addMinutes(5),
        );

        return $fallback;
    }

    private function requestSummary(
        string $key,
        string $baseUrl,
        string $model,
        string $contextJson,
    ): ?AiExplanationResult {
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
                                'content' => 'Anda adalah fitur penjelasan SkillPath AI. Keputusan utama sudah dihitung oleh sistem berbasis data dan aturan. Tugas Anda hanya menjelaskan hasil tersebut dengan Bahasa Indonesia yang alami dan mudah dipahami. Gunakan hanya target karier, skor kemampuan, target, gap, priority score, status, alasan, dan prasyarat yang diberikan. Jangan membuat skill, nilai, kemampuan, fakta, roadmap, materi, proyek, atau hubungan prasyarat baru. Jangan mengubah urutan prioritas. Jangan memberi jaminan kesiapan kerja. Tulis tepat satu paragraf tanpa Markdown, tanpa JSON, tanpa judul, maksimal 120 kata. Utamakan kemampuan dengan gap dan priority score tertinggi serta jelaskan alasannya.',
                            ],
                            [
                                'role' => 'user',
                                'content' => $contextJson,
                            ],
                        ],
                        'temperature' => 0.2,
                        'max_tokens' => 220,
                        'provider' => [
                            'allow_fallbacks' => true,
                        ],
                    ],
                );

            if (! $response->successful()) {
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

            $summary = $this->normalizeSummary(
                $response->json(
                    'choices.0.message.content',
                ),
            );

            if ($summary === null) {
                Log::warning(
                    'OpenRouter skill gap response was rejected.',
                    [
                        'requested_model' => $model,
                        'resolved_model' => $response->json(
                            'model',
                        ),
                        'finish_reason' => $response->json(
                            'choices.0.finish_reason',
                        ),
                        'content' => Str::limit(
                            (string) $response->json(
                                'choices.0.message.content',
                                '',
                            ),
                            500,
                            '',
                        ),
                    ],
                );

                return null;
            }

            $responseModel = $response->json(
                'model',
            );

            $resolvedModel = is_string($responseModel)
                && trim($responseModel) !== ''
                    ? trim($responseModel)
                    : $model;

            return new AiExplanationResult(
                $summary,
                true,
                $resolvedModel,
            );
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

    private function normalizeSummary(
        mixed $content,
    ): ?string {
        if (! is_string($content)) {
            return null;
        }

        $content = trim(
            $content,
        );

        if ($content === '') {
            return null;
        }

        $content = preg_replace(
            '/^```(?:json|text)?\s*|\s*```$/iu',
            '',
            $content,
        );

        if (! is_string($content)) {
            return null;
        }

        $decoded = json_decode(
            $content,
            true,
        );

        if (
            is_array($decoded)
            && is_string(
                $decoded['summary'] ?? null,
            )
        ) {
            $content = $decoded['summary'];
        }

        $content = str_replace(
            [
                '**',
                '__',
                '`',
            ],
            '',
            $content,
        );

        $content = preg_replace(
            '/^(?:ringkasan|summary)\s*:\s*/iu',
            '',
            $content,
        );

        if (! is_string($content)) {
            return null;
        }

        $content = preg_replace(
            '/\s+/u',
            ' ',
            $content,
        );

        if (! is_string($content)) {
            return null;
        }

        $content = trim(
            $content,
        );

        if (
            $content === ''
            || ! $this->looksIndonesian(
                $content,
            )
        ) {
            return null;
        }

        return Str::limit(
            $content,
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
            '/\b(?:yang|dan|untuk|dengan|dari|pada|adalah|karena|anda|kamu|kemampuan|belajar|prioritas|utama|saat|masih|selisih|setelah|perlu|berikutnya|kesiapan|proyek|materi|kendala|waktu|target|nilai|penguatan|risiko|langkah|evaluasi|jadwal|progres|perkembangan|hasil|pengguna|tercatat|memiliki|menjadi|berada|dapat|belum|lebih|sesuai|kesenjangan|karier|diprioritaskan|memenuhi|fondasi|lanjutan|penguasaan|dasar|terhadap|sehingga|terutama|berfokus|meningkatkan|menuju|skor|urutan|prasyarat|keterampilan|berdasarkan|dibutuhkan|dipelajari)\b/u',
            $text,
            $indonesianMatches,
        );

        $englishCount = preg_match_all(
            '/\b(?:the|and|for|with|from|this|that|your|you|is|are|was|were|to|of|in|on|because|after|before|current|learning|should|needs|need|next|improve|improved|based|using|use|has|have|still|result|results|successfully|created|development|strength|risk|step|steps|weekly|minutes|recorded)\b/u',
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
