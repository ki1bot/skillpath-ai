<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have a
    | conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env(
            'GOOGLE_REDIRECT_URI',
            '/auth/google/callback',
        ),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env(
            'FACEBOOK_REDIRECT_URI',
            '/auth/facebook/callback',
        ),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),

        'model' => env(
            'GEMINI_MODEL',
            'gemini-3.5-flash-lite',
        ),

        'fallback_models' => array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        (string) env(
                            'GEMINI_FALLBACK_MODELS',
                            'gemini-3.1-flash-lite',
                        ),
                    ),
                ),
            ),
        ),

        'base_url' => env(
            'GEMINI_BASE_URL',
            'https://generativelanguage.googleapis.com/v1beta',
        ),
    ],

    'ai' => [
        'request_timeout' => max(
            3,
            (int) env('AI_REQUEST_TIMEOUT', 30),
        ),

        'attempt_timeout' => max(
            2,
            (int) env('AI_ATTEMPT_TIMEOUT', 10),
        ),

        'connect_timeout' => max(
            1,
            (int) env('AI_CONNECT_TIMEOUT', 5),
        ),

        'failure_cache_seconds' => max(
            1,
            (int) env('AI_FAILURE_CACHE_SECONDS', 10),
        ),
    ],

    'openrouter' => [
        'key' => env('OPENROUTER_API_KEY'),

        'model' => env(
            'OPENROUTER_MODEL',
            'nex-agi/nex-n2.5-pro:free',
        ),

        'fallback_models' => array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        (string) env(
                            'OPENROUTER_FALLBACK_MODELS',
                            'nvidia/nemotron-3-ultra-550b-a55b:free',
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

    'tokenrouter' => [
        'key' => env('TOKENROUTER_API_KEY'),

        'model' => env(
            'TOKENROUTER_MODEL',
            '',
        ),

        'fallback_models' => array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        (string) env(
                            'TOKENROUTER_FALLBACK_MODELS',
                            '',
                        ),
                    ),
                ),
            ),
        ),

        'base_url' => env(
            'TOKENROUTER_BASE_URL',
            'https://api.tokenrouter.com/v1',
        ),
    ],

    'xkiro' => [
        'key' => env('XKIRO_API_KEY'),

        'model' => env(
            'XKIRO_MODEL',
            'qwen/qwen3.8-max:free',
        ),

        'fallback_models' => array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        (string) env(
                            'XKIRO_FALLBACK_MODELS',
                            'mistralai/mistral-large-2512',
                        ),
                    ),
                ),
            ),
        ),

        'base_url' => env(
            'XKIRO_BASE_URL',
            'https://api.xkiro.com/v1',
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
