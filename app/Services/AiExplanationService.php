<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class AiExplanationService
{
    public function skillGapSummary(User $user, array $analysis): string
    {
        $priorities = collect($analysis)
            ->filter(fn (array $item) => $item['gap'] > 0)
            ->take(4)
            ->values();

        if ($priorities->isEmpty()) {
            return 'Kemampuan inti Anda sudah memenuhi target internal untuk jalur karier ini. Fokus berikutnya adalah memperkuat bukti melalui proyek dan evaluasi.';
        }

        $fallback = $this->fallback($priorities->all());
        $key = config('services.openai.key');

        if (! is_string($key) || $key === '') {
            return $fallback;
        }

        $cacheKey = 'skill-gap-explanation:'.$user->id.':'.sha1(json_encode($priorities->all()));

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($user, $priorities, $fallback, $key) {
            try {
                $payload = $priorities->map(fn (array $item) => [
                    'skill' => $item['name'],
                    'current' => $item['current'],
                    'target' => $item['target'],
                    'gap' => $item['gap'],
                    'priority' => $item['priority'],
                    'prerequisites' => collect($item['prerequisites'])->pluck('name')->all(),
                ])->all();

                $response = Http::withToken($key)
                    ->acceptJson()
                    ->timeout(12)
                    ->post('https://api.openai.com/v1/responses', [
                        'model' => config('services.openai.model', 'gpt-5-mini'),
                        'instructions' => 'Anda adalah pendamping belajar SkillPath AI. Jelaskan data yang diberikan saja. Jangan membuat skill, nilai, atau roadmap baru. Gunakan Bahasa Indonesia yang ringkas, konkret, dan tidak berlebihan.',
                        'input' => 'Target karier: '.($user->targetCareer?->name ?? '-')."\nData prioritas: ".json_encode($payload, JSON_UNESCAPED_UNICODE),
                        'max_output_tokens' => 260,
                    ]);

                if (! $response->successful()) {
                    return $fallback;
                }

                $text = collect($response->json('output', []))
                    ->flatMap(fn (array $item) => $item['content'] ?? [])
                    ->firstWhere('type', 'output_text')['text'] ?? null;

                return is_string($text) && trim($text) !== ''
                    ? trim($text)
                    : $fallback;
            } catch (Throwable) {
                return $fallback;
            }
        });
    }

    private function fallback(array $priorities): string
    {
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
