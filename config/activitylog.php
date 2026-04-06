<?php

use Spatie\Activitylog\Actions\CleanActivityLogAction;
use Spatie\Activitylog\Actions\LogActivityAction;

return [
    'enabled' => true,
    'clean_after_days' => 365,
    'default_log_name' => 'default',
    'default_auth_driver' => null,
    'include_soft_deleted_subjects' => true,
    'activity_model' => App\Models\Activity::class,
    'default_except_attributes' => [],
    'buffer' => ['enabled' => false],
    'actions' => [
        'log_activity' => LogActivityAction::class,
        'clean_log' => CleanActivityLogAction::class,
    ],
];
