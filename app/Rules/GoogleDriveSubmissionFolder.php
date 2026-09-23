<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GoogleDriveSubmissionFolder implements ValidationRule
{
    private const FOLDER_MIME_TYPE = 'application/vnd.google-apps.folder';

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
            $fail('Link tugas harus berupa URL Google Drive yang valid.');

            return;
        }

        $parts = parse_url($url);

        if (! is_array($parts)) {
            $fail('Link tugas harus berupa URL Google Drive yang valid.');

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
            $fail('Link folder Google Drive harus menggunakan HTTPS.');

            return;
        }

        if (
            isset($parts['user'])
            || isset($parts['pass'])
        ) {
            $fail('Link Google Drive tidak boleh memuat kredensial pada URL.');

            return;
        }

        if ($host !== 'drive.google.com') {
            $fail('Pengumpulan tugas harus menggunakan link dari drive.google.com.');

            return;
        }

        if (! (bool) config(
            'services.google.verify_submission_folder',
            true,
        )) {
            return;
        }

        $folderId = $this->extractFolderId(
            (string) ($parts['path'] ?? ''),
        );

        if ($folderId === null) {
            $fail('Gunakan link folder Google Drive, bukan link file.');

            return;
        }

        $apiKey = trim(
            (string) config(
                'services.google.drive_api_key',
                '',
            ),
        );

        if ($apiKey === '') {
            $fail('Pemeriksaan folder Google Drive belum dikonfigurasi. Hubungi admin.');

            return;
        }

        try {
            $folderResponse = Http::acceptJson()
                ->connectTimeout(5)
                ->timeout(10)
                ->get(
                    "https://www.googleapis.com/drive/v3/files/{$folderId}",
                    [
                        'fields' => 'id,name,mimeType,trashed',
                        'supportsAllDrives' => 'true',
                        'key' => $apiKey,
                    ],
                );

            if (! $folderResponse->successful()) {
                $fail('Folder Google Drive tidak dapat diperiksa. Pastikan aksesnya diatur ke "Siapa saja yang memiliki link".');

                return;
            }

            if (
                $folderResponse->json('mimeType')
                !== self::FOLDER_MIME_TYPE
            ) {
                $fail('Link yang dikirim harus mengarah ke folder Google Drive.');

                return;
            }

            if ($folderResponse->json('trashed') === true) {
                $fail('Folder Google Drive sudah berada di sampah dan tidak dapat digunakan.');

                return;
            }

            $contentsResponse = Http::acceptJson()
                ->connectTimeout(5)
                ->timeout(10)
                ->get(
                    'https://www.googleapis.com/drive/v3/files',
                    [
                        'q' => "'{$folderId}' in parents and trashed = false",
                        'pageSize' => 1,
                        'fields' => 'files(id,name,mimeType)',
                        'supportsAllDrives' => 'true',
                        'includeItemsFromAllDrives' => 'true',
                        'key' => $apiKey,
                    ],
                );

            if (! $contentsResponse->successful()) {
                $fail('Isi folder Google Drive tidak dapat diperiksa. Pastikan folder dapat dibuka oleh siapa saja yang memiliki link.');

                return;
            }

            $files = $contentsResponse->json('files');

            if (! is_array($files) || $files === []) {
                $fail('Folder Google Drive masih kosong. Masukkan hasil pengerjaan tugas ke dalam folder sebelum mengumpulkan.');
            }
        } catch (ConnectionException) {
            $fail('Google Drive sedang tidak dapat diperiksa. Silakan coba lagi.');
        }
    }

    private function extractFolderId(string $path): ?string
    {
        if (
            preg_match(
                '~(?:^|/)folders/([A-Za-z0-9_-]+)(?:/|$)~',
                $path,
                $matches,
            ) !== 1
        ) {
            return null;
        }

        return (string) $matches[1];
    }
}
