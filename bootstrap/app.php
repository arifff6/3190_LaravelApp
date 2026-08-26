<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Matikan StartSession bawaan agar tidak menyentuh session storage disk read-only
        $middleware->web(remove: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->booting(function (Application $app) {
        $app->useStoragePath('/tmp/storage');

        // Force override semua driver agar tidak pernah bernilai empty string
        $config = $app->make('config');
        $config->set('session.driver', 'cookie');
        $config->set('session.lifetime', 120);
        $config->set('cache.default', 'array');
        $config->set('cache.stores.array', ['driver' => 'array', 'serialize' => false]);
        $config->set('logging.default', 'stderr');
        $config->set('logging.channels.stderr', [
            'driver' => 'monolog',
            'handler' => \Monolog\Handler\StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => ['stream' => 'php://stderr'],
        ]);
        $config->set('queue.default', 'sync');
        $config->set('view.compiled', '/tmp/storage/framework/views');
    })
    ->create();