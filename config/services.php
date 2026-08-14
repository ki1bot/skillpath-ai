<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'openrouter' => [
        'key' => env('OPENROUTER_API_KEY'),

        'model' => env(
            'OPENROUTER_MODEL',
            'openrouter/free',
        ),

        'fallback_models' => array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        (string) env(
                            'OPENROUTER_FALLBACK_MODELS',
                            '',
                        ),
                    ),
                ),
            ),
        ),

        'base_url' => env(
            'OPENROUTER_BASE_URL',
            'https://openrouter.ai/api/v1',
        ),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env(
            'AWS_DEFAULT_REGION',
            'us-east-1',
        ),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env(
                'SLACK_BOT_USER_OAUTH_TOKEN',
            ),
            'channel' => env(
                'SLACK_BOT_USER_DEFAULT_CHANNEL',
            ),
        ],
    ],

];
