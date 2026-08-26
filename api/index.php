<?php

// 1. Buat direktori sementara di /tmp
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

// 2. Set environment paths
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes-v7.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');

// 3. Autoload & Inisialisasi Aplikasi
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath('/tmp/storage');

// 4. Force config langsung pada container sebelum request diproses
$app->booted(function ($app) {
    $config = $app->make('config');
    $config->set('session.driver', 'cookie');
    $config->set('session.lifetime', 120);
    $config->set('cache.default', 'array');
    $config->set('logging.default', 'stderr');
});

// Jika booted sudah terlewat, inject langsung ke config repository
if ($app->bound('config')) {
    $config = $app->make('config');
    $config->set('session.driver', 'cookie');
    $config->set('session.lifetime', 120);
    $config->set('cache.default', 'array');
    $config->set('logging.default', 'stderr');
}

// 5. Eksekusi Request
$app->handleRequest(Illuminate\Http\Request::capture());