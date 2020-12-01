<?php

return [

    'backup' => [

        'name' => 'arbor',

        'source' => [

            'files' => [
                'include' => [
                    base_path(),
                ],
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],
                'follow_links' => false,
                'ignore_unreadable_directories' => false,
            ],

            'databases' => [
                'mysql',
            ],

        ],

        'database_dump_compressor' => null,

        'destination' => [

            'filename_prefix' => 'backup-',

            'disks' => [
                'backup',
            ],
        ],

        'temporary_directory' => storage_path('backup-temp'),
    ],

    'notifications' => [

        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailed::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFound::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailed::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessful::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFound::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessful::class => ['slack'],
        ],

        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'slack' => [
            'webhook_url' => env('SLACK_WEBHOOK_URL'),

            'channel' => null,
            'username' => null,
            'icon' => null,
        ],

    ],

    'monitor_backups' => [
        [
            'name' => 'arbor',
            'disks' => ['backup'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [
            'keep_all_backups_for_days' => 7,
            'keep_daily_backups_for_days' => 16,
            'keep_weekly_backups_for_weeks' => 8,
            'keep_monthly_backups_for_months' => 4,
            'keep_yearly_backups_for_years' => 2,
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],

];
