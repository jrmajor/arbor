<?php

return [

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('S3_ACCESS_KEY_ID'),
            'secret' => env('S3_SECRET_ACCESS_KEY'),
            'region' => env('S3_DEFAULT_REGION'),
            'bucket' => env('S3_BUCKET'),
            'endpoint' => env('S3_URL'),
        ],

        'backup' => [
            'driver' => 's3',
            'key' => env('BACKUP_S3_ACCESS_KEY_ID'),
            'secret' => env('BACKUP_S3_SECRET_ACCESS_KEY'),
            'region' => env('BACKUP_S3_DEFAULT_REGION'),
            'bucket' => env('BACKUP_S3_BUCKET'),
            'endpoint' => env('BACKUP_S3_URL'),
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
