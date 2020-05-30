<?php

namespace App\Notifications\Backup;

use App\Notifications\Backup\BaseNotification;
use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\HealthyBackupWasFound as HealthyBackupWasFoundEvent;

class HealthyBackupWasFound extends BaseNotification
{
    /** @var \Spatie\Backup\Events\HealthyBackupWasFound */
    protected $event;

    public function __construct(HealthyBackupWasFoundEvent $event)
    {
        $this->event = $event;
    }

    public function toTelegram(): TelegramMessage
    {
        $message =
            e(trans('backup::notifications.healthy_backup_found_subject_title', ['application_name' => $this->applicationName()], 'en'))
            ."\n";

        foreach ($this->backupDestinationProperties() as $key => $val) {
            $message .= "\n<b>".e($key).':</b> '.e($val);
        }

        return (new TelegramMessage)
            ->to(config('backup.notifications.telegram.to'))
            ->content($message)
            ->options(['parse_mode' => 'html']);
    }
}
