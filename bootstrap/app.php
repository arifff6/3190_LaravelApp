<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Siapkan folder /tmp untuk filesystem read-only Vercel
$dirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Matikan session middleware stateful jika hanya render view statis/API di serverless
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

        // Force konfigurasi session, cache, dan log langsung di repository config
        $config = $app->make('config');
        $config->set([
            'session.driver' => 'array',
            'session.store' => 'array',
            'cache.default' => 'array',
            'cache.stores.array' => ['driver' => 'array', 'serialize' => false],
            'logging.default' => 'stderr',
            'logging.channels.stderr' => [
                'driver' => 'monolog',
                'handler' => \Monolog\Handler\StreamHandler::class,
                'formatter' => env('LOG_STDERR_FORMATTER'),
                'with' => ['stream' => 'php://stderr'],
            ],
            'view.compiled' => '/tmp/storage/framework/views',
        ]);
    })
    ->create();