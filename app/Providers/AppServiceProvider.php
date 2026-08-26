<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kunci semua driver agar tidak pernah bernilai string kosong ("")
        config([
            'session.driver' => 'cookie',
            'session.store' => 'cookie',
            'session.lifetime' => 120,
            'session.files' => '/tmp/storage/framework/sessions',
            'cache.default' => 'array',
            'cache.stores.array' => ['driver' => 'array', 'serialize' => false],
            'logging.default' => 'stderr',
            'logging.channels.stderr' => [
                'driver' => 'monolog',
                'handler' => \Monolog\Handler\StreamHandler::class,
                'formatter' => env('LOG_STDERR_FORMATTER'),
                'with' => ['stream' => 'php://stderr'],
            ],
            'queue.default' => 'sync',
            'view.compiled' => '/tmp/storage/framework/views',
        ]);
    }
}