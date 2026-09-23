<?php

namespace App\Services\Ai;

use Illuminate\Support\Str;

class AiInsightFormatter
{
    /**
     * @param  array<int, string>  $requiredTags
     */
    public function systemPrompt(
        string $task,
        array $requiredTags,
    ): string {
        return 'Anda adalah fitur AI SkillPath AI. Locale aplikasi saat ini adalah '
            .app()->getLocale()
            .'. Seluruh teks yang ditampilkan kepada pengguna wajib menggunakan Bahasa Indonesia. '
            .'Jangan menulis kalimat dalam Bahasa Inggris. Nama teknologi, framework, API, database, '
            .'bahasa pemrograman, library, atau istilah teknis yang umum boleh tetap menggunakan nama '
            .'aslinya. Gunakan hanya data yang diberikan. Jangan mengubah skor, hasil Assessment, status '
            .'progres, keputusan roadmap, kemampuan, proyek, materi, atau fakta lain. Jangan membuat data '
            .'yang tidak diberikan. Gunakan Bahasa Indonesia yang alami, jelas, dan ringkas. '
            .$task
            .' '
            .$this->outputInstruction($requiredTags);
    }

    /**
     * @param  array<int, string>  $requiredTags
     * @return array<string, mixed>
     */
    public function geminiJsonSchema(
        array $requiredTags,
    ): array {
        if ($requiredTags !== []) {
            return [
                'type' => 'object',
                'properties' => [
                    'progress' => [
                        'type' => 'string',
                    ],
                    'schedule' => [
                        'type' => 'string',
                    ],
                    'obstacles' => [
                        'type' => 'string',
                    ],
                ],
                'required' => [
                    'progress',
                    'schedule',
                    'obstacles',
                ],
                'additionalProperties' => false,
            ];
        }

        return [
            'type' => 'object',
            'properties' => [
                'content' => [
                    'type' => 'string',
                ],
            ],
            'required' => [
                'content',
            ],
            'additionalProperties' => false,
        ];
    }

    /**
     * @param  array<int, string>  $requiredTags
     */
    public function normalize(
        string $content,
        array $requiredTags,
    ): ?string {
        $content = $this->prepareContent(
            $content,
            $requiredTags,
        );

        if (
            $content === null
            || ! $this->looksIndonesian($content)
            || ! $this->hasRequiredTags(
                $content,
                $requiredTags,
            )
        ) {
            return null;
        }

        return $content;
    }

    /**
     * @param  array<int, string>  $requiredTags
     */
    public function validCachedContent(
        string $content,
        array $requiredTags,
    ): bool {
        return trim($content) !== ''
            && $this->looksIndonesian($content)
            && $this->hasRequiredTags(
                $content,
                $requiredTags,
            );
    }

    /**
     * @return array{
     *     progress: string,
     *     schedule: string,
     *     obstacles: string
     * }|null
     */
    public function sections(
        string $content,
    ): ?array {
        $result = [];

        foreach (
            [
                'progress' => 'PROGRESS',
                'schedule' => 'SCHEDULE',
                'obstacles' => 'OBSTACLES',
            ] as $key => $tag
        ) {
            if (
                preg_match(
                    '/<'
                        .$tag
                        .'>\s*(.*?)\s*<\/'
                        .$tag
                        .'>/si',
                    $content,
                    $matches,
                ) !== 1
            ) {
                return null;
            }

            $value = trim(
                (string) $matches[1],
            );

            if ($value === '') {
                return null;
            }

            $result[$key] = $value;
        }

        return [
            'progress' => $result['progress'],
            'schedule' => $result['schedule'],
            'obstacles' => $result['obstacles'],
        ];
    }

    /**
     * @param  array<int, string>  $requiredTags
     */
    private function outputInstruction(
        array $requiredTags,
    ): string {
        if ($requiredTags !== []) {
            return 'Kembalikan hanya JSON valid tanpa Markdown dengan tiga field string: progress, schedule, dan obstacles.';
        }

        return 'Kembalikan hanya JSON valid tanpa Markdown dengan satu field string: content.';
    }

    /**
     * @param  array<int, string>  $requiredTags
     */
    private function prepareContent(
        string $content,
        array $requiredTags,
    ): ?string {
        $content = $this->normalizeContent($content);

        if ($content === null) {
            return null;
        }

        $requiredTags = array_values(
            $requiredTags,
        );

        if (
            $requiredTags !== []
            && $this->hasRequiredTags(
                $content,
                $requiredTags,
            )
        ) {
            return $content;
        }

        $decoded = json_decode(
            $content,
            true,
        );

        if (! is_array($decoded)) {
            if ($requiredTags === []) {
                return $content;
            }

            return $this->parseProgressText(
                $content,
            );
        }

        if ($requiredTags === []) {
            $value = $decoded['content'] ?? null;

            return is_string($value)
                ? $this->normalizeContent($value)
                : null;
        }

        $progress = $decoded['progress'] ?? null;
        $schedule = $decoded['schedule'] ?? null;
        $obstacles = $decoded['obstacles'] ?? null;

        if (
            ! is_string($progress)
            || ! is_string($schedule)
            || ! is_string($obstacles)
        ) {
            return null;
        }

        $progress = $this->normalizeContent($progress);
        $schedule = $this->normalizeContent($schedule);
        $obstacles = $this->normalizeContent($obstacles);

        if (
            $progress === null
            || $schedule === null
            || $obstacles === null
        ) {
            return null;
        }

        return '<PROGRESS>'
            .$progress
            .'</PROGRESS>'
            .'<SCHEDULE>'
            .$schedule
            .'</SCHEDULE>'
            .'<OBSTACLES>'
            .$obstacles
            .'</OBSTACLES>';
    }

    private function parseProgressText(
        string $content,
    ): ?string {
        $patterns = [
            'progress' => '/(?:^|\n)\s*(?:progress|ringkasan perkembangan|perkembangan)\s*:?\s*(.*?)(?=\n\s*(?:schedule|saran jadwal belajar|jadwal)\s*:?|\z)/isu',

            'schedule' => '/(?:^|\n)\s*(?:schedule|saran jadwal belajar|jadwal)\s*:?\s*(.*?)(?=\n\s*(?:obstacles|pola kendala belajar|kendala)\s*:?|\z)/isu',

            'obstacles' => '/(?:^|\n)\s*(?:obstacles|pola kendala belajar|kendala)\s*:?\s*(.*?)\s*\z/isu',
        ];

        $result = [];

        foreach ($patterns as $key => $pattern) {
            if (
                preg_match(
                    $pattern,
                    $content,
                    $matches,
                ) !== 1
            ) {
                return null;
            }

            $value = $this->normalizeContent(
                (string) $matches[1],
            );

            if ($value === null) {
                return null;
            }

            $result[$key] = $value;
        }

        return '<PROGRESS>'
            .$result['progress']
            .'</PROGRESS>'
            .'<SCHEDULE>'
            .$result['schedule']
            .'</SCHEDULE>'
            .'<OBSTACLES>'
            .$result['obstacles']
            .'</OBSTACLES>';
    }

    private function normalizeContent(
        string $content,
    ): ?string {
        $content = trim($content);

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
            '/[ \t]+/u',
            ' ',
            $content,
        );

        if (! is_string($content)) {
            return null;
        }

        $content = preg_replace(
            "/\n{3,}/u",
            "\n\n",
            $content,
        );

        if (! is_string($content)) {
            return null;
        }

        $content = trim($content);

        return $content !== ''
            ? $content
            : null;
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
            '/\b(?:yang|dan|untuk|dengan|dari|pada|adalah|karena|anda|kamu|kemampuan|belajar|prioritas|utama|saat|masih|selisih|setelah|perlu|berikutnya|kesiapan|proyek|materi|kendala|waktu|nilai|penguatan|risiko|langkah|evaluasi|jadwal|progres|perkembangan|berhasil|dibuat|dikelompokkan|kekuatan|sudah|ditingkatkan|kerjakan|tugas|hasil|buat|tambahkan|latihan|bukti|pengguna|mingguan|menit|sesi|catatan|hambatan|berikut|tercatat|gunakan|memiliki|menjadi|berada|dapat|belum|lebih|sesuai|bagian|karier|pemahaman|dipelajari|diselesaikan|dikerjakan)\b/u',
            $text,
            $indonesianMatches,
        );

        $englishCount = preg_match_all(
            '/\b(?:the|and|for|with|from|this|that|your|you|is|are|was|were|to|of|in|on|because|after|before|current|learning|should|needs|need|next|improve|improved|based|using|use|has|have|still|result|results|successfully|created|grouped|development|strength|risk|step|steps|weekly|minutes|recorded|project|progress|schedule|obstacles)\b/u',
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

    /**
     * @param  array<int, string>  $requiredTags
     */
    private function hasRequiredTags(
        string $content,
        array $requiredTags,
    ): bool {
        foreach ($requiredTags as $tag) {
            if (
                ! str_contains(
                    $content,
                    '<'.$tag.'>',
                )
                || ! str_contains(
                    $content,
                    '</'.$tag.'>',
                )
            ) {
                return false;
            }
        }

        return true;
    }
}
