<?php

return [

    'name' => 'Arbor',

    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'https://arbor.test'),
    'asset_url' => env('ASSET_URL'),

    'timezone' => 'Europe/Warsaw',
    'locale' => 'en',
    'available_locales' => ['pl', 'en', 'de'],
    'fallback_locale' => 'en',
    'faker_locale' => 'pl_PL',

    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],

];
