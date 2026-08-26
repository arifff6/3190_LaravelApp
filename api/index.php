<?php

use Illuminate\Http\Request;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1. Siapkan folder wajib di /tmp
$dirs = [
    '/tmp/storage/app',
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

// 2. Override environment paths
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes-v7.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');

// 3. Load Autoloader & Bootstrap App
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath('/tmp/storage');

// 4. Injeksi langsung default driver ke Configuration Repository
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

// 5. Handle Request
$app->handleRequest(Request::capture());