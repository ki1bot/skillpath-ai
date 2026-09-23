<?php

namespace Tests\Unit;

use App\Rules\GoogleDriveSubmissionFolder;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Client\Request as HttpRequest;
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

        Http::preventStrayRequests();
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

        Http::assertNothingSent();
    }

    public function test_file_link_is_still_rejected_when_remote_verification_is_disabled(): void
    {
        config()->set(
            'services.google.verify_submission_folder',
            false,
        );

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

        Http::assertNothingSent();
    }

    public function test_valid_folder_url_can_skip_remote_verification(): void
    {
        config()->set(
            'services.google.verify_submission_folder',
            false,
        );

        $validator = $this->validator(
            'https://drive.google.com/drive/folders/test-folder',
        );

        $this->assertTrue(
            $validator->passes(),
        );

        Http::assertNothingSent();
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
            'Folder Google Drive tidak dapat diperiksa. '
                .'Pastikan aksesnya diatur ke '
                .'"Siapa saja yang memiliki link".',
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
            'Folder Google Drive masih kosong. '
                .'Masukkan hasil pengerjaan tugas ke dalam folder '
                .'sebelum mengumpulkan.',
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

    public function test_resource_key_is_forwarded_to_google_drive_requests(): void
    {
        $expectedHeader = 'resource-folder/0-example-resource-key';

        Http::fake(
            function (HttpRequest $request) use ($expectedHeader) {
                $this->assertTrue(
                    $request->hasHeader(
                        'X-Goog-Drive-Resource-Keys',
                        $expectedHeader,
                    ),
                );

                if (
                    str_contains(
                        $request->url(),
                        '/drive/v3/files/resource-folder',
                    )
                ) {
                    return Http::response([
                        'id' => 'resource-folder',
                        'name' => 'Tugas Lama',
                        'mimeType' => 'application/vnd.google-apps.folder',
                        'trashed' => false,
                    ]);
                }

                return Http::response([
                    'files' => [
                        [
                            'id' => 'work-file',
                            'name' => 'hasil-tugas.pdf',
                            'mimeType' => 'application/pdf',
                        ],
                    ],
                ]);
            },
        );

        $validator = $this->validator(
            'https://drive.google.com/drive/folders/'
                .'resource-folder'
                .'?resourcekey=0-example-resource-key'
                .'&usp=sharing',
        );

        $this->assertTrue(
            $validator->passes(),
        );

        Http::assertSentCount(2);
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
