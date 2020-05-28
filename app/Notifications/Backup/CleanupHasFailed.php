<?php

namespace App\Notifications\Backup;

use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\CleanupHasFailed as CleanupHasFailedEvent;
use Spatie\Backup\Notifications\BaseNotification;

class CleanupHasFailed extends BaseNotification
{
    /** @var \Spatie\Backup\Events\CleanupHasFailed */
    protected $event;

    public function __construct(CleanupHasFailedEvent $event)
    {
        $this->event = $event;
    }

    public function toTelegram(): TelegramMessage
    {
        return (new TelegramMessage)
            ->to(config('backup.notifications.telegram.to'))
            ->content(
                '*'.trans('backup::notifications.cleanup_failed_subject', ['application_name' => $this->applicationName()])."*\n"
                .trans('backup::notifications.exception_message_title').': '
                .$this->event->exception->getMessage()
            );
    }
}
