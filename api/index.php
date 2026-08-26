<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1. Buat folder temporary di /tmp (satu-satunya lokasi writable di Vercel)
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

// 2. Override SEMUA path cache sistem Laravel ke /tmp SEBELUM autoloader
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes-v7.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');

$_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes-v7.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';

// 3. Load Autoloader & Bootstrap App
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Belokkan storage path & bootstrap path instance Laravel
$app->useStoragePath('/tmp/storage');
$app->useBootstrapPath('/tmp/bootstrap');

// 5. Intercept Kernel HTTP
$kernel = $app->make(Kernel::class);
$request = Request::capture();

try {
    $kernel->bootstrap();

    // Set konfigurasi fallback yang aman di memory
    $config = $app->make('config');
    $config->set('session.driver', 'cookie');
    $config->set('cache.default', 'array');
    $config->set('logging.default', 'errorlog');
    $config->set('queue.default', 'sync');

    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h2 style='color:red; font-family:sans-serif;'>🚨 ERROR ASLI DITEMUKAN 🚨</h2>";
    echo "<div style='font-family:monospace; background:#111; color:#0f0; padding:20px;'>";
    echo "<b>Pesan Error:</b> " . $e->getMessage() . "<br><br>";
    echo "<b>Lokasi File:</b> " . $e->getFile() . " (Baris: " . $e->getLine() . ")<br><br>";
    echo "<b>Stack Trace:</b><br>" . nl2br($e->getTraceAsString());
    echo "</div>";
}