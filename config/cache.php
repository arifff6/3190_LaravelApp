<?php

$default = env('CACHE_STORE') ?: env('CACHE_DRIVER');
if (empty($default)) {
    $default = 'array';
}

return [
    'default' => $default,
    'stores' => [
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
    ],
    'prefix' => 'laravel_cache',
];