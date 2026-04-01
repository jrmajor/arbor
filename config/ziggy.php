<?php

return [
    'output' => [
        'path' => 'resources/js/ziggy/index.js',
        'types' => App\Support\ZiggyTypesOutput::class,
    ],
    'only' => [
        'login',
        'logout',
        'password.*',
        'people.*',
        'marriages.*',
        'dashboard.*',
        'settings.*',
        'locale.*',
        'ajax.*',
    ],
];
