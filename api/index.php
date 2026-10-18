<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$storagePath = '/tmp/skillpath-ai-storage';

$directories = [
    $storagePath.'/app/private',
    $storagePath.'/app/public',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
];

foreach ($directories as $directory) {
    if (
        !is_dir($directory)
        && !mkdir($directory, 0775, true)
        && !is_dir($directory)
    ) {
        throw new RuntimeException(
            'Unable to create directory: '.$directory
        );
    }
}

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';

$app->useStoragePath($storagePath);

$app->handleRequest(Request::capture());
