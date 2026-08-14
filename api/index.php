<?php

declare(strict_types=1);

error_reporting(E_ALL);

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', 'php://stderr');

$tempPath = rtrim(
    sys_get_temp_dir(),
    DIRECTORY_SEPARATOR,
);

$storagePath = $tempPath
    .DIRECTORY_SEPARATOR
    .'skillpath-ai-storage';

$viewPath = $storagePath
    .DIRECTORY_SEPARATOR
    .'framework'
    .DIRECTORY_SEPARATOR
    .'views';

$directories = [
    $storagePath,
    $storagePath.DIRECTORY_SEPARATOR.'app',
    $storagePath.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'private',
    $storagePath.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public',
    $storagePath.DIRECTORY_SEPARATOR.'framework',
    $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'cache',
    $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'data',
    $storagePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'sessions',
    $viewPath,
    $storagePath.DIRECTORY_SEPARATOR.'logs',
];

foreach ($directories as $directory) {
    if (
        ! is_dir($directory)
        && ! mkdir($directory, 0777, true)
        && ! is_dir($directory)
    ) {
        throw new RuntimeException(
            sprintf(
                'Unable to create runtime directory: %s',
                $directory,
            ),
        );
    }
}

$runtimePaths = [
    'LARAVEL_STORAGE_PATH' => $storagePath,
    'APP_CONFIG_CACHE' => $tempPath.DIRECTORY_SEPARATOR.'skillpath-ai-config.php',
    'APP_EVENTS_CACHE' => $tempPath.DIRECTORY_SEPARATOR.'skillpath-ai-events.php',
    'APP_PACKAGES_CACHE' => $tempPath.DIRECTORY_SEPARATOR.'skillpath-ai-packages.php',
    'APP_ROUTES_CACHE' => $tempPath.DIRECTORY_SEPARATOR.'skillpath-ai-routes.php',
    'APP_SERVICES_CACHE' => $tempPath.DIRECTORY_SEPARATOR.'skillpath-ai-services.php',
    'VIEW_COMPILED_PATH' => $viewPath,
];

foreach ($runtimePaths as $key => $value) {
    putenv($key.'='.$value);

    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

register_shutdown_function(function (): void {
    $error = error_get_last();

    if ($error === null) {
        return;
    }

    $fatalErrors = [
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_COMPILE_ERROR,
        E_USER_ERROR,
        E_RECOVERABLE_ERROR,
    ];

    if (! in_array($error['type'], $fatalErrors, true)) {
        return;
    }

    error_log(
        '[SKILLPATH_FATAL] '.json_encode(
            [
                'type' => $error['type'],
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
            ],
            JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE,
        ),
    );
});

try {
    require __DIR__.'/../public/index.php';
} catch (Throwable $exception) {
    error_log(
        '[SKILLPATH_BOOTSTRAP_EXCEPTION] '.json_encode(
            [
                'class' => $exception::class,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
            JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE,
        ),
    );

    throw $exception;
}
