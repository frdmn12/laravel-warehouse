<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Temporary diagnostic: something in this chain throws a
// BindingResolutionException for "view" specifically on Vercel (not
// reproducible with a local `composer install` + built-in server), and
// it happens before this file even returns — narrow down which call.
$builder = Application::configure(basePath: dirname(__DIR__));
error_log('[diag] configure() ok');

$builder = $builder->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
);
error_log('[diag] withRouting() ok');

$builder = $builder->withMiddleware(function (Middleware $middleware): void {
    $middleware->statefulApi();
});
error_log('[diag] withMiddleware() ok');

$builder = $builder->withExceptions(function (Exceptions $exceptions): void {
    //
});
error_log('[diag] withExceptions() ok');

$app = $builder->create();
error_log('[diag] create() ok');

return $app;
