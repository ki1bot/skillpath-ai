<?php

namespace App\Services\Ai;

use Illuminate\Support\Str;

class AiExplanationFormatter
{
    public function systemPrompt(): string
    {
        return 'Anda adalah fitur penjelasan SkillPath AI. '
            .'Keputusan utama sudah dihitung oleh sistem berbasis data dan aturan. '
            .'Tugas Anda hanya menjelaskan hasil tersebut dengan Bahasa Indonesia yang alami dan mudah dipahami. '
            .'Gunakan hanya target karier, skor kemampuan, target, gap, priority score, status, dan prasyarat yang diberikan. '
            .'Jangan membuat skill, nilai, kemampuan, fakta, roadmap, materi, proyek, atau hubungan prasyarat baru. '
            .'Jangan mengubah urutan prioritas. Jangan memberi jaminan kesiapan kerja. '
            .'Tulis tepat satu paragraf tanpa Markdown, tanpa JSON, tanpa judul, maksimal 120 kata. '
            .'Utamakan kemampuan dengan gap dan priority score tertinggi serta jelaskan alasannya.';
    }

    public function normalizeSummary(
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

        $content = trim($content);

        if (
            $content === ''
            || ! $this->looksIndonesian($content)
        ) {
            return null;
        }

        return Str::limit(
            $content,
            700,
            '',
        );
    }

    public function validCachedSummary(
        string $summary,
    ): bool {
        return trim($summary) !== ''
            && $this->looksIndonesian(
                $summary,
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
