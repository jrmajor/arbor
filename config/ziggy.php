<?php

return [

    'output' => [
        'path' => 'resources/js/types/ziggy.js',
    ],

    'skip-route-function' => true,

    'only' => [
        'login',
        'logout',
        'password.*',
        'people.*',
        'marriages.*',
        'dashboard.*',
        'settings',
        'locale.*',
    ],

];
