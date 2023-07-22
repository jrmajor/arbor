<?php

return [

    'default' => 'errors',

    'deprecations' => [
        'channel' => 'deprecations',
        'trace' => false,
    ],

    'channels' => [
        'errors' => [
            'driver' => 'stack',
            'channels' => ['daily_errors', 'larabug'],
            'ignore_exceptions' => false,
        ],

        'deprecations' => [
            'driver' => 'stack',
            'channels' => ['daily_deprecations', 'larabug'],
            'ignore_exceptions' => false,
        ],

        'daily_errors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/errors.log'),
            'level' => 'debug',
            'days' => 14,
            'replace_placeholders' => true,
        ],

        'daily_deprecations' => [
            'driver' => 'daily',
            'path' => storage_path('logs/deprecations.log'),
            'level' => 'debug',
            'days' => 14,
            'replace_placeholders' => true,
        ],

        'larabug' => [
            'driver' => 'larabug',
        ],
    ],

];
