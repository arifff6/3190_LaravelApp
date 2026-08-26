<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "<h2>--- DIAGNOSTIK LARAVEL DI VERCEL ---</h2>";

// 1. Cek Permission Folder /tmp
try {
    $testFile = '/tmp/test_write.txt';
    file_put_contents($testFile, 'OK');
    if (file_exists($testFile)) {
        echo "<p style='color:green;'>[PASS] 1. Folder /tmp bisa ditulisi (Writable).</p>";
    }
} catch (\Throwable $e) {
    die("<p style='color:red;'>[FAIL] 1. Folder /tmp error: " . $e->getMessage() . "</p>");
}

// 2. Cek Composer Vendor Autoload
try {
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        die("<p style='color:red;'>[FAIL] 2. vendor/autoload.php tidak ditemukan di server!</p>");
    }
    require $autoloadPath;
    echo "<p style='color:green;'>[PASS] 2. Composer Autoload berhasil dimuat.</p>";
} catch (\Throwable $e) {
    die("<p style='color:red;'>[FAIL] 2. Autoload error: " . $e->getMessage() . "</p>");
}

// 3. Cek Environment Variables (APP_KEY)
$appKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? null);
if (empty($appKey)) {
    echo "<p style='color:orange;'>[WARNING] 3. APP_KEY kosong di Environment Variables Vercel!</p>";
} else {
    echo "<p style='color:green;'>[PASS] 3. APP_KEY terdeteksi.</p>";
}

// 4. Cek Load bootstrap/app.php
try {
    $bootstrapPath = __DIR__ . '/../bootstrap/app.php';
    if (!file_exists($bootstrapPath)) {
        die("<p style='color:red;'>[FAIL] 4. bootstrap/app.php tidak ditemukan!</p>");
    }
    
    // Set path cache sementara sebelum load app
    putenv('VIEW_COMPILED_PATH=/tmp');
    putenv('APP_CONFIG_CACHE=/tmp/config.php');
    putenv('APP_ROUTES_CACHE=/tmp/routes.php');
    
    $app = require_once $bootstrapPath;
    echo "<p style='color:green;'>[PASS] 4. bootstrap/app.php berhasil diinisialisasi (" . get_class($app) . ").</p>";
} catch (\Throwable $e) {
    die("<p style='color:red;'>[FAIL] 4. Gagal bootstrap app: " . $e->getMessage() . "<br><pre>" . $e->getTraceAsString() . "</pre></p>");
}

echo "<h3>Semua komponen dasar normal. Tinggal mengeksekusi kernel HTTP.</h3>";