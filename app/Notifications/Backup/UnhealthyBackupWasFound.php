<?php

namespace App\Notifications\Backup;

use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\UnhealthyBackupWasFound as UnhealthyBackupWasFoundEvent;
use Spatie\Backup\Notifications\BaseNotification;
use Spatie\Backup\Tasks\Monitor\HealthCheckFailure;

class UnhealthyBackupWasFound extends BaseNotification
{
    /** @var \Spatie\Backup\Events\UnhealthyBackupWasFound */
    protected $event;

    public function __construct(UnhealthyBackupWasFoundEvent $event)
    {
        $this->event = $event;
    }

    public function toTelegram(): TelegramMessage
    {
        if (! $this->failure()->wasUnexpected()) {
            return (new TelegramMessage)
                ->to(config('backup.notifications.telegram.to'))
                ->content(
                    '*'.trans('backup::notifications.unhealthy_backup_found_subject', ['application_name' => $this->applicationName()])."*\n"
                    .$this->problemDescription()
                );
        } else {
            return (new TelegramMessage)
                ->to(config('backup.notifications.telegram.to'))
                ->content(
                    '*'.trans('backup::notifications.unhealthy_backup_found_subject', ['application_name' => $this->applicationName()])."*\n"
                    .$this->problemDescription()."\n"
                    ."Health check: "
                    .$this->failure()->healthCheck()->name()."\n"
                    .trans('backup::notifications.exception_message_title').': '
                    .$this->faillure()->exception()->getMessage()
                );
        }
    }

    protected function problemDescription(): string
    {
        if ($this->failure()->wasUnexpected()) {
            return trans('backup::notifications.unhealthy_backup_found_unknown');
        }

        return $this->failure()->exception()->getMessage();
    }

    protected function failure(): HealthCheckFailure
    {
        return $this->event->backupDestinationStatus->getHealthCheckFailure();
    }
}
