<?php

return [

    'name' => 'Arbor',

    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'https://arbor.test'),

    // todo: change to UTC
    'timezone' => 'Europe/Warsaw',
    'locale' => 'en',
    'available_locales' => ['pl', 'en', 'de'],
    'fallback_locale' => 'en',
    'faker_locale' => 'pl_PL',

    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => array_filter(
        explode(',', (string) env('APP_PREVIOUS_KEYS', '')),
    ),

    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],

];
