<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExternalEvidenceUrl implements ValidationRule
{
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail,
    ): void {
        if (! is_string($value) || trim($value) === '') {
            return;
        }

        $url = trim($value);

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            $fail('Tautan bukti harus berupa URL yang valid.');

            return;
        }

        $parts = parse_url($url);

        if (! is_array($parts)) {
            $fail('Tautan bukti harus berupa URL yang valid.');

            return;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($scheme !== 'https') {
            $fail('Tautan bukti harus menggunakan HTTPS.');

            return;
        }

        if ($host === '') {
            $fail('Tautan bukti harus memiliki host yang valid.');

            return;
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            $fail('Tautan bukti tidak boleh memuat kredensial pada URL.');

            return;
        }

        $normalizedHost = trim($host, '[]');

        if (
            $normalizedHost === 'localhost'
            || str_ends_with($normalizedHost, '.localhost')
            || str_ends_with($normalizedHost, '.local')
            || str_ends_with($normalizedHost, '.internal')
        ) {
            $fail('Tautan bukti harus dapat diakses melalui host eksternal.');

            return;
        }

        $isIp = filter_var(
            $normalizedHost,
            FILTER_VALIDATE_IP,
        ) !== false;

        if ($isIp) {
            $isPublicIp = filter_var(
                $normalizedHost,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
            ) !== false;

            if (! $isPublicIp) {
                $fail('Tautan bukti tidak boleh menggunakan alamat IP privat atau reserved.');
            }

            return;
        }

        if (! str_contains($normalizedHost, '.')) {
            $fail('Tautan bukti harus menggunakan host eksternal yang valid.');
        }
    }
}
