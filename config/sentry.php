<?php

use Psl\Async\Exception\CancelledException;
use Psl\Shell;

try {
    $gitHash = Shell\execute(
        'git',
        ['rev-parse', '--short=7', 'HEAD'],
        base_path(),
    );
} catch (Shell\Exception\ExceptionInterface|CancelledException) {
    $gitHash = '';
}

$gitHash = trim($gitHash);

if (strlen($gitHash) !== 7) {
    $gitHash = null;
}

return [

    'dsn' => env('SENTRY_PHP_DSN'),
    'release' => $gitHash,
    'sample_rate' => 1.0,
    'ignore_exceptions' => [],
    'ignore_transactions' => [],

    'breadcrumbs' => [
        'logs' => true,
        'cache' => true,
        'sql_queries' => true,
        'sql_bindings' => false,
        'queue_info' => true,
        'command_info' => true,
        'http_client_requests' => true,
        'notifications' => true,
    ],

];
