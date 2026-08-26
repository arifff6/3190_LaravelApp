<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1. Buat folder sementara di /tmp
$dirs = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework',
    '/tmp/storage/framework/cache',
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

// 2. Set environment paths
putenv('LOG_CHANNEL=stderr');
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes-v7.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');

// 3. Autoload & Inisialisasi
require __DIR__ . '/../vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Binding storage path ke /tmp
    $app->useStoragePath('/tmp/storage');

    // Eksekusi HTTP Request
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $request = Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    
    $response->send();
    
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    echo "<h1>Error saat Merender Halaman:</h1>";
    echo "<p><strong>Pesan:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " di baris <strong>" . $e->getLine() . "</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}