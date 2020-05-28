<?php

namespace App\Notifications\Backup;

use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\HealthyBackupWasFound as HealthyBackupWasFoundEvent;
use Spatie\Backup\Notifications\BaseNotification;

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
        return (new TelegramMessage)
            ->to(config('backup.notifications.telegram.to'))
            ->content('*'.trans('backup::notifications.healthy_backup_found_subject', ['application_name' => $this->applicationName(), 'disk_name' => $this->diskName()], 'en').'*');
    }
}
