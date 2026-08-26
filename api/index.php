<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Never leak raw PHP notices/warnings/deprecations into the response body —
// Laravel's own logging (LOG_CHANNEL=stderr) is the right place for these,
// not the page a visitor sees. Tied to APP_DEBUG so a fatal error that
// happens before Laravel's own exception handler is registered (e.g.
// during these early requires) can still be seen by temporarily setting
// APP_DEBUG=true in the Vercel project's environment variables — no code
// change/redeploy needed to toggle it.
ini_set('display_errors', getenv('APP_DEBUG') === 'true' ? '1' : '0');

// Always send PHP-level errors (including fatals before Laravel's own
// exception handler is registered) to stderr, unconditionally — this is
// distinct from display_errors above: it's what makes those errors show
// up in `vercel logs` / the Vercel runtime log viewer even when
// APP_DEBUG is off and nothing is shown in the response body.
ini_set('log_errors', '1');
ini_set('error_log', 'php://stderr');

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

try {
    /** @var Application $app */
    $app = require __DIR__ . '/../bootstrap/app.php';

    $app->useStoragePath($storagePath);

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    // A failure this early (bootstrapping the app / registering service
    // providers) can happen before Laravel's own exception handler and
    // the "view" binding it needs to render an error page exist yet —
    // trying to let Laravel handle it just produces a second, unrelated
    // BindingResolutionException that buries the real cause. Log a
    // compact one-line summary of the whole chain instead, since the
    // full multi-KB stack trace this exception carries gets truncated
    // by the log pipeline before the actual class/message at its root
    // ever shows up.
    for ($t = $e; $t !== null; $t = $t->getPrevious()) {
        error_log(sprintf('[boot] %s: %s in %s:%d', $t::class, $t->getMessage(), $t->getFile(), $t->getLine()));
    }

    http_response_code(500);
    exit;
}
