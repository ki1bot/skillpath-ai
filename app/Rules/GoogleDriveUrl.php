<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GoogleDriveUrl implements ValidationRule
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
            $fail('Tautan bukti harus berupa URL Google Drive yang valid.');

            return;
        }

        $parts = parse_url($url);

        if (! is_array($parts)) {
            $fail('Tautan bukti harus berupa URL Google Drive yang valid.');

            return;
        }

        $scheme = strtolower(
            (string) ($parts['scheme'] ?? ''),
        );

        $host = strtolower(
            rtrim(
                (string) ($parts['host'] ?? ''),
                '.',
            ),
        );

        if ($scheme !== 'https') {
            $fail('Tautan Google Drive harus menggunakan HTTPS.');

            return;
        }

        if (
            isset($parts['user'])
            || isset($parts['pass'])
        ) {
            $fail('Tautan Google Drive tidak boleh memuat kredensial pada URL.');

            return;
        }

        if ($host !== 'drive.google.com') {
            $fail('Bukti harus menggunakan tautan dari drive.google.com.');

            return;
        }

        $path = trim(
            (string) ($parts['path'] ?? ''),
            '/',
        );

        if ($path === '') {
            $fail('Tautan Google Drive harus mengarah ke file atau folder yang valid.');
        }
    }
}
