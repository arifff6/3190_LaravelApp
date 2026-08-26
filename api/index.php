<?php

use Illuminate\Http\Request;
use Illuminate\Session\NullSessionHandler;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1. Buat folder temporary wajib di /tmp
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

// 2. Set environment paths
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes-v7.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');

// 3. Autoload & Inisialisasi
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath('/tmp/storage');
$app->useBootstrapPath('/tmp/bootstrap');

// 4. KUNCI DRIVER KOSONG (Cegah pemanggilan createDriver() tanpa argumen)
$app->booting(function () use ($app) {
    // Force config default
    $config = $app->make('config');
    $config->set('session.driver', 'array');
    $config->set('session.store', 'array');
    $config->set('cache.default', 'array');
    $config->set('logging.default', 'stderr');
    
    // Inject handler untuk driver string kosong jika dipanggil
    if ($app->bound('session')) {
        $manager = $app->make('session');
        $manager->extend('', function () {
            return new NullSessionHandler();
        });
        $manager->extend('null', function () {
            return new NullSessionHandler();
        });
    }
});

// 5. Eksekusi Request
$app->handleRequest(Request::capture());