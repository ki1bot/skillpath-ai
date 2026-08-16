<?php

namespace Tests\Unit;

use App\Rules\ExternalEvidenceUrl;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ExternalEvidenceUrlTest extends TestCase
{
    public function test_public_https_url_is_accepted(): void
    {
        $validator = Validator::make(
            [
                'evidence' => 'https://github.com/ki1bot/skillpath-ai',
            ],
            [
                'evidence' => [
                    'required',
                    new ExternalEvidenceUrl,
                ],
            ],
        );

        $this->assertTrue(
            $validator->passes(),
        );
    }

    public function test_http_url_is_rejected(): void
    {
        $validator = Validator::make(
            [
                'evidence' => 'http://example.com/evidence',
            ],
            [
                'evidence' => [
                    'required',
                    new ExternalEvidenceUrl,
                ],
            ],
        );

        $this->assertTrue(
            $validator->fails(),
        );
    }

    public function test_localhost_is_rejected(): void
    {
        $validator = Validator::make(
            [
                'evidence' => 'https://localhost/evidence',
            ],
            [
                'evidence' => [
                    'required',
                    new ExternalEvidenceUrl,
                ],
            ],
        );

        $this->assertTrue(
            $validator->fails(),
        );
    }

    public function test_private_ip_is_rejected(): void
    {
        $validator = Validator::make(
            [
                'evidence' => 'https://192.168.1.10/evidence',
            ],
            [
                'evidence' => [
                    'required',
                    new ExternalEvidenceUrl,
                ],
            ],
        );

        $this->assertTrue(
            $validator->fails(),
        );
    }
}
