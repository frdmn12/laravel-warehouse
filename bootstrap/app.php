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

            // Temporary diagnostic: the message alone doesn't say who's
            // calling Manager::createDriver() with 0 arguments — nothing
            // in vendor/ calls it that way, so the caller must be a couple
            // frames up (past __call's forwarding). Top few frames only,
            // kept short enough to survive the log pipeline's truncation.
            foreach (array_slice($e->getTrace(), 0, 4) as $i => $frame) {
                error_log(sprintf(
                    '[trace] #%d %s%s%s() at %s:%s',
                    $i,
                    $frame['class'] ?? '',
                    $frame['type'] ?? '',
                    $frame['function'] ?? '',
                    $frame['file'] ?? '?',
                    $frame['line'] ?? '?',
                ));
            }
        });
    })->create();
