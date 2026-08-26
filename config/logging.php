<?php

use Monolog\Handler\StreamHandler;

return [
    'default' => 'stderr',
    'channels' => [
        'stderr' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],
    ],
];