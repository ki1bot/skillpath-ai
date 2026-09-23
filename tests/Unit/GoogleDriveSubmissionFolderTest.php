<?php

namespace Tests\Unit;

use App\Rules\GoogleDriveSubmissionFolder;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class GoogleDriveSubmissionFolderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set([
            'services.google.drive_api_key' => 'test-drive-api-key',
            'services.google.verify_submission_folder' => true,
        ]);
    }

    public function test_google_drive_file_link_is_rejected(): void
    {
        $validator = $this->validator(
            'https://drive.google.com/file/d/example-file/view',
        );

        $this->assertTrue(
            $validator->fails(),
        );

        $this->assertSame(
            'Gunakan link folder Google Drive, bukan link file.',
            $validator
                ->errors()
                ->first('evidence'),
        );
    }

    public function test_private_or_unavailable_folder_is_rejected(): void
    {
        Http::fakeSequence()
            ->push([], 404);

        $validator = $this->validator(
            'https://drive.google.com/drive/folders/private-folder',
        );

        $this->assertTrue(
            $validator->fails(),
        );

        $this->assertSame(
            'Folder Google Drive tidak dapat diperiksa. Pastikan aksesnya diatur ke "Siapa saja yang memiliki link".',
            $validator
                ->errors()
                ->first('evidence'),
        );
    }

    public function test_empty_google_drive_folder_is_rejected(): void
    {
        Http::fakeSequence()
            ->push([
                'id' => 'empty-folder',
                'name' => 'Tugas Database',
                'mimeType' => 'application/vnd.google-apps.folder',
                'trashed' => false,
            ])
            ->push([
                'files' => [],
            ]);

        $validator = $this->validator(
            'https://drive.google.com/drive/folders/empty-folder',
        );

        $this->assertTrue(
            $validator->fails(),
        );

        $this->assertSame(
            'Folder Google Drive masih kosong. Masukkan hasil pengerjaan tugas ke dalam folder sebelum mengumpulkan.',
            $validator
                ->errors()
                ->first('evidence'),
        );
    }

    public function test_non_empty_public_google_drive_folder_is_accepted(): void
    {
        Http::fakeSequence()
            ->push([
                'id' => 'filled-folder',
                'name' => 'Tugas Database',
                'mimeType' => 'application/vnd.google-apps.folder',
                'trashed' => false,
            ])
            ->push([
                'files' => [
                    [
                        'id' => 'work-file',
                        'name' => 'hasil-tugas.pdf',
                        'mimeType' => 'application/pdf',
                    ],
                ],
            ]);

        $validator = $this->validator(
            'https://drive.google.com/drive/folders/filled-folder',
        );

        $this->assertTrue(
            $validator->passes(),
        );
    }

    private function validator(string $value): ValidatorContract
    {
        return Validator::make(
            [
                'evidence' => $value,
            ],
            [
                'evidence' => [
                    'required',
                    new GoogleDriveSubmissionFolder,
                ],
            ],
        );
    }
}
