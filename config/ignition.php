<?php

return [

    'editor' => env('IGNITION_EDITOR', 'sublime'),

    'theme' => env('IGNITION_THEME', 'auto'),

    'enable_share_button' => false,

    'ignored_solution_providers' => [],

    'enable_runnable_solutions' => false,

    'remote_sites_path' => env('IGNITION_REMOTE_SITES_PATH', ''),
    'local_sites_path' => env('IGNITION_LOCAL_SITES_PATH', ''),

    'housekeeping_endpoint_prefix' => '_ignition',

];
