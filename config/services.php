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
            (int) env('AI_REQUEST_TIMEOUT', 20),
        ),

        'attempt_timeout' => max(
            2,
            (int) env('AI_ATTEMPT_TIMEOUT', 4),
        ),

        'connect_timeout' => max(
            1,
            (int) env('AI_CONNECT_TIMEOUT', 3),
        ),

        'failure_cache_seconds' => max(
            1,
            (int) env('AI_FAILURE_CACHE_SECONDS', 5),
        ),
    ],

    'openrouter' => [
        'key' => env('OPENROUTER_API_KEY'),

        'model' => env(
            'OPENROUTER_MODEL',
            'minimax/minimax-m3:free',
        ),

        'fallback_models' => array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        (string) env(
                            'OPENROUTER_FALLBACK_MODELS',
                            'openrouter/free',
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
            'z-ai/glm-5.3-free',
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
            'deepseek/deepseek-v4-pro',
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
                'SLACK_BOT_USER_DEFAULT_CHANNEL',
            ),
            'channel' => env(
                'SLACK_BOT_USER_DEFAULT_CHANNEL',
            ),
        ],
    ],

];
