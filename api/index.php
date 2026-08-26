<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 1. HAPUS FILE CACHE LAMA (Biang kerok Vercel)
$staleCaches = [
    __DIR__ . '/../bootstrap/cache/config.php',
    __DIR__ . '/../bootstrap/cache/events.php',
    __DIR__ . '/../bootstrap/cache/packages.php',
    __DIR__ . '/../bootstrap/cache/routes-v7.php',
    __DIR__ . '/../bootstrap/cache/services.php',
];
foreach ($staleCaches as $cache) {
    if (file_exists($cache)) {
        @unlink($cache);
    }
}

// 2. SIAPKAN FOLDER /tmp
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

// 3. FORCE PATH KE /tmp
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

// 4. JALANKAN APP LARAVEL
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');

// 5. INTERCEPT KERNEL & PAKSA KONFIGURASI
$kernel = $app->make(Kernel::class);
$request = Request::capture();

try {
    // Bootstrap memuat file bawaan
    $kernel->bootstrap();
    
    // TIMPA KONFIGURASI LANGSUNG DI MEMORY (Pasti jalan 100%)
    $config = $app->make('config');
    $config->set('session.driver', 'cookie');
    $config->set('cache.default', 'array');
    $config->set('logging.default', 'errorlog'); // Paksa pakai log bawaan PHP
    $config->set('queue.default', 'sync');
    $config->set('database.default', 'sqlite'); // Hindari crash DB kosong
    
    // Eksekusi Request
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
    
} catch (\Throwable $e) {
    // TAMPILKAN ERROR ASLINYA KE LAYAR
    http_response_code(500);
    echo "<h2 style='color:red; font-family:sans-serif;'>🚨 ERROR ASLI DITEMUKAN 🚨</h2>";
    echo "<div style='font-family:monospace; background:#111; color:#0f0; padding:20px;'>";
    echo "<b>Pesan Error:</b> " . $e->getMessage() . "<br><br>";
    echo "<b>Lokasi File:</b> " . $e->getFile() . " (Baris: " . $e->getLine() . ")<br><br>";
    echo "<b>Stack Trace:</b><br>" . nl2br($e->getTraceAsString());
    echo "</div>";
}