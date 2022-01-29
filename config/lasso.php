<?php

return [

    'compiler' => [
        'script' => 'yarn run production',
        'timeout' => 300,
        'output' => 'progress',
        'excluded_files' => [],
        'excluded_directories' => [],
    ],

    'storage' => [
        'disk' => 'assets',
        'upload_to' => 'lasso',
        'environment' => env('LASSO_ENV', null),
        'prefix' => env('LASSO_PREFIX', ''),
        'max_bundles' => 5,
    ],

    'webhooks' => [
        'publish' => [],
        'pull' => [],
    ],

    'public_path' => public_path(),

];
