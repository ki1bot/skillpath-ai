<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
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

        /*
         * Bentuk URL folder tetap wajib divalidasi meskipun
         * pemeriksaan API dinonaktifkan.
         *
         * Jadi GOOGLE_DRIVE_VERIFY_SUBMISSION_FOLDER=false hanya
         * mematikan request eksternal ke Google, bukan mematikan
         * validasi bahwa URL benar-benar merupakan folder.
         */
        $folderId = $this->extractFolderId(
            (string) ($parts['path'] ?? ''),
        );

        if ($folderId === null) {
            $fail('Gunakan link folder Google Drive, bukan link file.');

            return;
        }

        if (! (bool) config(
            'services.google.verify_submission_folder',
            true,
        )) {
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

        /*
         * Beberapa folder Google Drive hasil link sharing lama
         * membutuhkan resource key.
         *
         * Contoh:
         *
         * https://drive.google.com/drive/folders/ID
         * ?resourcekey=0-xxxxxxxx
         */
        $resourceKey = $this->extractResourceKey(
            (string) ($parts['query'] ?? ''),
        );

        try {
            $folderResponse = $this->driveRequest(
                $folderId,
                $resourceKey,
            )->get(
                'https://www.googleapis.com/drive/v3/files/'
                    .rawurlencode($folderId),
                [
                    'fields' => 'id,name,mimeType,trashed',
                    'supportsAllDrives' => 'true',
                    'key' => $apiKey,
                ],
            );

            if (! $folderResponse->successful()) {
                $fail(
                    'Folder Google Drive tidak dapat diperiksa. '
                    .'Pastikan aksesnya diatur ke '
                    .'"Siapa saja yang memiliki link".',
                );

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

            /*
             * Google secara resmi mendukung pencarian isi public folder
             * menggunakan files.list + API key.
             */
            $contentsResponse = $this->driveRequest(
                $folderId,
                $resourceKey,
            )->get(
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
                $fail(
                    'Isi folder Google Drive tidak dapat diperiksa. '
                    .'Pastikan folder dapat dibuka oleh siapa saja '
                    .'yang memiliki link.',
                );

                return;
            }

            $files = $contentsResponse->json('files');

            if (! is_array($files) || $files === []) {
                $fail(
                    'Folder Google Drive masih kosong. '
                    .'Masukkan hasil pengerjaan tugas ke dalam folder '
                    .'sebelum mengumpulkan.',
                );
            }
        } catch (ConnectionException) {
            $fail('Google Drive sedang tidak dapat diperiksa. Silakan coba lagi.');
        }
    }

    private function driveRequest(
        string $folderId,
        ?string $resourceKey,
    ): PendingRequest {
        $request = Http::acceptJson()
            ->connectTimeout(5)
            ->timeout(10);

        if ($resourceKey === null) {
            return $request;
        }

        return $request->withHeaders([
            'X-Goog-Drive-Resource-Keys' => $folderId
                .'/'
                .$resourceKey,
        ]);
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

    private function extractResourceKey(string $query): ?string
    {
        if ($query === '') {
            return null;
        }

        parse_str(
            $query,
            $parameters,
        );

        foreach ($parameters as $name => $value) {
            if (
                ! is_string($name)
                || strtolower($name) !== 'resourcekey'
                || ! is_string($value)
            ) {
                continue;
            }

            $resourceKey = trim($value);

            /*
             * Resource key dari Google menggunakan karakter yang aman
             * untuk dikirim pada header.
             *
             * Validasi ini juga mencegah karakter newline/header
             * injection dari URL input pengguna.
             */
            if (
                $resourceKey === ''
                || preg_match(
                    '/^[A-Za-z0-9_-]+$/',
                    $resourceKey,
                ) !== 1
            ) {
                return null;
            }

            return $resourceKey;
        }

        return null;
    }
}
