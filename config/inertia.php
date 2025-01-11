<?php

return [

    'ssr' => [
        'enabled' => true,
        'url' => 'http://127.0.0.1:13715', // one more than the default port
        // 'bundle' => base_path('bootstrap/ssr/ssr.mjs'),
    ],

    'testing' => [
        'ensure_pages_exist' => true,
        'page_paths' => [resource_path('js/Pages')],
        'page_extensions' => ['svelte'],
    ],

    'history' => [
        'encrypt' => false,
    ],

];
