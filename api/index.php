<?php

use Illuminate\Http\Request;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1. Bersihkan semua env kosong yang dikirim Vercel
$criticalKeys = [
    'SESSION_DRIVER' => 'cookie',
    'SESSION_STORE' => 'cookie',
    'CACHE_STORE' => 'array',
    'CACHE_DRIVER' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'LOG_CHANNEL' => 'stderr',
    'MAIL_MAILER' => 'array',
];

foreach ($criticalKeys as $key => $defaultVal) {
    if (!isset($_ENV[$key]) || trim((string)$_ENV[$key]) === '') {
        $_ENV[$key] = $defaultVal;
        $_SERVER[$key] = $defaultVal;
        putenv("{$key}={$defaultVal}");
    }
}

// 2. Buat folder temporary di /tmp untuk Serverless Vercel
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

// 3. Set path cache dan storage
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes-v7.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');

// 4. Autoload Composer
require __DIR__ . '/../vendor/autoload.php';

// 5. Bootstrap App
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath('/tmp/storage');

// 6. Tangani Request
$app->handleRequest(Request::capture());