<?php

namespace App\Notifications\Backup;

use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\BackupHasFailed as BackupHasFailedEvent;
use Spatie\Backup\Notifications\BaseNotification;

class BackupHasFailed extends BaseNotification
{
    /** @var \Spatie\Backup\Events\BackupHasFailed */
    protected $event;

    public function __construct(BackupHasFailedEvent $event)
    {
        $this->event = $event;
    }

    public function toTelegram(): TelegramMessage
    {
        return (new TelegramMessage)
            ->to(config('backup.notifications.telegram.to'))
            ->content(
                '*'.trans('backup::notifications.backup_failed_subject', ['application_name' => $this->applicationName()], 'en')."*\n"
                .trans('backup::notifications.exception_message_title', [], 'en').': '
                .$this->event->exception->getMessage()
            );
    }
}
