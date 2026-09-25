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
        'drive_api_key' => env('GOOGLE_DRIVE_API_KEY'),
        'verify_submission_folder' => env(
            'GOOGLE_DRIVE_VERIFY_SUBMISSION_FOLDER',
            true,
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
            'gemini-3.6-flash',
        ),

        'fallback_models' => array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        (string) env(
                            'GEMINI_FALLBACK_MODELS',
                            'gemini-3.5-flash-lite',
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
            (int) env('AI_REQUEST_TIMEOUT', 60),
        ),

        'attempt_timeout' => max(
            2,
            (int) env('AI_ATTEMPT_TIMEOUT', 25),
        ),

        'connect_timeout' => max(
            1,
            (int) env('AI_CONNECT_TIMEOUT', 5),
        ),

        'failure_cache_seconds' => max(
            1,
            (int) env('AI_FAILURE_CACHE_SECONDS', 10),
        ),

        'provider_order' => array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        (string) env(
                            'AI_PROVIDER_ORDER',
                            'juanrouter,openrouter,xkiro,gemini',
                        ),
                    ),
                ),
            ),
        ),

        'health_cooldown_seconds' => max(
            10,
            (int) env(
                'AI_HEALTH_COOLDOWN_SECONDS',
                45,
            ),
        ),

        'health_max_cooldown_seconds' => max(
            30,
            (int) env(
                'AI_HEALTH_MAX_COOLDOWN_SECONDS',
                300,
            ),
        ),

        'health_state_seconds' => max(
            60,
            (int) env(
                'AI_HEALTH_STATE_SECONDS',
                600,
            ),
        ),
    ],

    'juanrouter' => [
        'key' => env('JUANROUTER_API_KEY'),

        'model' => env(
            'JUANROUTER_MODEL',
            'gpt-6-luna',
        ),

        'fallback_models' => array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        (string) env(
                            'JUANROUTER_FALLBACK_MODELS',
                            'gpt-5.6-luna',
                        ),
                    ),
                ),
            ),
        ),

        'base_url' => env(
            'JUANROUTER_BASE_URL',
            'https://router.juan.web.id/v1',
        ),

        'reasoning_effort' => env(
            'JUANROUTER_REASONING_EFFORT',
            'low',
        ),
    ],

    'public_chat' => [
        'enabled' => env(
            'PUBLIC_CHAT_ENABLED',
            true,
        ),

        'key' => env('JUANROUTER_CHAT_API_KEY')
            ?: env('JUANROUTER_API_KEY'),

        'model' => env(
            'JUANROUTER_CHAT_MODEL',
            'gpt-6-luna',
        ),

        'fallback_models' => array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        (string) env(
                            'JUANROUTER_CHAT_FALLBACK_MODELS',
                            'gpt-5.6-luna',
                        ),
                    ),
                ),
            ),
        ),

        'base_url' => env('JUANROUTER_CHAT_BASE_URL')
            ?: env(
                'JUANROUTER_BASE_URL',
                'https://router.juan.web.id/v1',
            ),

        'request_timeout' => max(
            10,
            (int) env(
                'PUBLIC_CHAT_REQUEST_TIMEOUT',
                50,
            ),
        ),

        'max_history_messages' => min(
            12,
            max(
                2,
                (int) env(
                    'PUBLIC_CHAT_MAX_HISTORY_MESSAGES',
                    8,
                ),
            ),
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
