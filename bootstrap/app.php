<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // LOG_CHANNEL defaults to writing under storage/logs, which api/index.php
        // redirects to an ephemeral /tmp tree on Vercel — fine for Laravel's own
        // framework-level logging, but exceptions deserve to show up in the
        // Vercel runtime log viewer (stderr) directly, not just in a log file
        // that vanishes with the Lambda instance.
        $exceptions->reportable(function (\Throwable $e): void {
            error_log(sprintf('[report] %s: %s in %s:%d', $e::class, $e->getMessage(), $e->getFile(), $e->getLine()));
        });
    })->create();
