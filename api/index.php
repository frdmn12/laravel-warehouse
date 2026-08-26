<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Never leak raw PHP notices/warnings/deprecations into the response body —
// Laravel's own logging (LOG_CHANNEL=stderr) is the right place for these,
// not the page a visitor sees.
ini_set('display_errors', '0');

require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Writable storage on Vercel
|--------------------------------------------------------------------------
|
| The deployed bundle's filesystem is read-only at runtime, except for
| /tmp. Laravel still needs somewhere to compile Blade views and (if
| LOG_CHANNEL isn't overridden to "stderr") write logs, so every cold
| start re-creates a scratch storage tree under /tmp before the app
| boots. This directory is ephemeral and not shared across invocations.
|
*/
$storagePath = '/tmp/storage';

foreach ([
    $storagePath,
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/testing',
    $storagePath . '/framework/views',
    $storagePath . '/logs',
] as $path) {
    if (! is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

/** @var Application $app */
$app = require __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($storagePath);

$app->handleRequest(Request::capture());
