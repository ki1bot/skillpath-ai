<?php

declare(strict_types=1);

error_reporting(E_ALL);

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', 'php://stderr');

register_shutdown_function(function (): void {
    $error = error_get_last();

    if ($error === null) {
        return;
    }

    error_log(json_encode([
        'type' => $error['type'],
        'message' => $error['message'],
        'file' => $error['file'],
        'line' => $error['line'],
    ], JSON_UNESCAPED_SLASHES));
});

try {
    require __DIR__.'/../public/index.php';
} catch (Throwable $exception) {
    error_log(sprintf(
        '%s: %s in %s:%d%s%s',
        $exception::class,
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        PHP_EOL,
        $exception->getTraceAsString(),
    ));

    throw $exception;
}
