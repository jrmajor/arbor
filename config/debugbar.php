<?php

return [

    'enabled' => env('DEBUGBAR_ENABLED'),
    'except' => [],

    'storage' => [
        'enabled' => true,
        'driver' => 'file', // redis, file, pdo, custom
        'path' => storage_path('debugbar'), // For file driver
        'connection' => null,   // Leave null for default connection (Redis/PDO)
        'provider' => '', // Instance of StorageInterface for custom driver
    ],

    'include_vendors' => true,

    'capture_ajax' => true,
    'add_ajax_timing' => false,

    'error_handler' => false,

    'clockwork' => false,

    'collectors' => [
        'phpinfo' => true,  // Php version
        'messages' => true,  // Messages
        'time' => true,  // Time Datalogger
        'memory' => true,  // Memory usage
        'exceptions' => true,  // Exception displayer
        'log' => true,  // Logs from Monolog (merged in messages if enabled)
        'db' => true,  // Show database (PDO) queries and bindings
        'views' => true,  // Views with their data
        'route' => true,  // Current route information
        'auth' => false, // Display Laravel authentication status
        'gate' => true,  // Display Laravel Gate checks
        'session' => true,  // Display session data
        'symfony_request' => true,  // Only one can be enabled..
        'mail' => true,  // Catch mail messages
        'laravel' => false, // Laravel version and environment
        'events' => false, // All events fired
        'default_request' => false, // Regular or special Symfony request logger
        'logs' => false, // Add the latest log messages
        'files' => false, // Show the included files
        'config' => false, // Display config settings
        'cache' => false, // Display cache events
        'models' => true,  // Display models
    ],

    'options' => [
        'auth' => [
            'show_name' => true,   // Also show the users name/email in the debugbar
        ],
        'db' => [
            'with_params' => true,   // Render SQL with the parameters substituted
            'backtrace' => true,   // Use a backtrace to find the origin of the query in your files.
            'timeline' => false,  // Add the queries to the timeline
            'explain' => [                 // Show EXPLAIN output on queries
                'enabled' => false,
                'types' => ['SELECT'],     // // workaround ['SELECT'] only. https://github.com/barryvdh/laravel-debugbar/issues/888 ['SELECT', 'INSERT', 'UPDATE', 'DELETE']; for MySQL 5.6.3+
            ],
            'hints' => true,    // Show hints for common mistakes
        ],
        'mail' => [
            'full_log' => false,
        ],
        'views' => [
            'data' => false,    // Note: Can slow down the application, because the data can be quite large..
        ],
        'route' => [
            'label' => true,  // show complete route on bar
        ],
        'logs' => [
            'file' => null,
        ],
        'cache' => [
            'values' => true, // collect cache values
        ],
    ],

    'inject' => true,

    'route_prefix' => '_debugbar',

    'route_domain' => null,
];
