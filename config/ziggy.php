<?php

return [

    'output' => [
        'path' => 'resources/js/helpers/ziggy.js',
    ],

    'skip-route-function' => true,

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
