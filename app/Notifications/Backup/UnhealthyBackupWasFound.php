<?php

namespace App\Notifications\Backup;

use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\UnhealthyBackupWasFound as UnhealthyBackupWasFoundEvent;
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
        $message =
            e(trans('backup::notifications.unhealthy_backup_found_subject_title', ['application_name' => $this->applicationName(), 'problem' => $this->problemDescription()], 'en'))
            ."\n";

        if ($this->failure()->wasUnexpected()) {
            $message .= "\n<b>Health check:</b> ".e($this->failure()->healthCheck()->name());
            $message .= "\n<b>".e(trans('backup::notifications.exception_message_title', [], 'en')).':</b> '.e($this->failure()->exception()->getMessage());
        }

        foreach ($this->backupDestinationProperties() as $key => $val) {
            $message .= "\n<b>".e($key).':</b> '.e($val);
        }

        return (new TelegramMessage)
            ->to(config('backup.notifications.telegram.to'))
            ->content($message)
            ->options(['parse_mode' => 'html']);
    }

    protected function problemDescription(): string
    {
        if ($this->failure()->wasUnexpected()) {
            return trans('backup::notifications.unhealthy_backup_found_unknown', [], 'en');
        }

        return $this->failure()->exception()->getMessage();
    }

    protected function failure(): HealthCheckFailure
    {
        return $this->event->backupDestinationStatus->getHealthCheckFailure();
    }
}
