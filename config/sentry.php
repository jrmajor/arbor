<?php

use Psl\Shell;
use Psl\Shell\Exception\FailedExecutionException;
use Psl\Shell\Exception\TimeoutException;

try {
    $gitHash = Shell\execute(
        'git',
        ['log', '--pretty=%h', '-n1', 'HEAD'],
        base_path('.git'),
    );
} catch (FailedExecutionException|TimeoutException) {
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
        'livewire' => true,
        'sql_queries' => true,
        'sql_bindings' => false,
        'queue_info' => true,
        'command_info' => true,
        'http_client_requests' => true,
        'notifications' => true,
    ],

];
