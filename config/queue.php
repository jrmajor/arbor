<?php

return [

    'default' => env('QUEUE_CONNECTION', 'sync'),

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
            'after_commit' => false,
        ],

    ],

    'batching' => [
        'database' => env('DB_CONNECTION', 'default'),
        'table' => 'job_batches',
    ],

    'failed' => [
        'driver' => 'database',
        'database' => env('DB_CONNECTION', 'default'),
        'table' => 'failed_jobs',
    ],

];
