<?php

return [
    'idle_timeout_minutes' => (int) env('AUTH_IDLE_TIMEOUT', 10),

    'user_manager_email' => env(
        'USER_MANAGER_EMAIL',
        'f8goodspoof@gmail.com',
    ),
];
