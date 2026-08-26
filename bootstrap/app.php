<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Bind storage path dan force default driver untuk serverless
$app->useStoragePath('/tmp/storage');

$app->booting(function () use ($app) {
    $config = $app->make('config');
    $config->set('session.driver', 'cookie');
    $config->set('cache.default', 'array');
    $config->set('logging.default', 'stderr');
});

return $app;