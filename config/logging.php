<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    */

    'channels' => [
        // 默认堆栈通道
        'stack' => [
            'driver' => 'stack',
            'channels' => ['application'],
            'ignore_exceptions' => false,
        ],

        // 应用主日志
        'application' => [
            'driver' => 'daily',
            'path' => storage_path('logs/application.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 30,
            'locking' => true,
        ],

        // 请求日志
        'requests' => [
            'driver' => 'daily',
            'path' => storage_path('logs/requests.log'),
            'level' => 'info',
            'days' => 14,
        ],

        // 数据库日志
        'database' => [
            'driver' => 'daily',
            'path' => storage_path('logs/database.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        // 安全日志
        'security' => [
            'driver' => 'daily',
            'path' => storage_path('logs/security.log'),
            'level' => 'info',
            'days' => 90,
        ],

        // 调试日志（仅开发环境）
        'debug' => [
            'driver' => 'daily',
            'path' => storage_path('logs/debug.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        // 错误日志
        'error' => [
            'driver' => 'single',
            'path' => storage_path('logs/error.log'),
            'level' => 'error',
        ],

        // 控制台日志
        'console' => [
            'driver' => 'daily',
            'path' => storage_path('logs/console.log'),
            'level' => 'info',
            'days' => 7,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Level Threshold
    |--------------------------------------------------------------------------
    |
    | You can enable log level threshold to only log messages above a certain
    | level. This is useful in production to avoid logging too many messages.
    |
    */

    'level' => env('LOG_LEVEL', 'debug'),

    /*
    |--------------------------------------------------------------------------
    | Log Stack Channel
    |--------------------------------------------------------------------------
    |
    | The stack channel is used when multiple log drivers are configured.
    |
    */

    'stack' => [
        'driver' => 'stack',
        'channels' => ['application'],
        'ignore_exceptions' => false,
    ],
];
