<?php

return [

    'login_key' => env('LB_KEY', 'LB_KEY'),

    'project_key' => env('LB_PROJECT_KEY', 'LB_PROJECT_KEY'),

    'environments' => ['production'],

    'lines_count' => 12,

    'sleep' => 60,

    'except' => [
        'Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException',
    ],

    'blacklist' => [
        'password',
        'password_confirmation',
    ],

];
