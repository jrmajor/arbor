<?php

namespace App\Notifications\Backup;

use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\CleanupHasFailed as CleanupHasFailedEvent;

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
        $message =
            e(trans('backup::notifications.cleanup_failed_subject', ['application_name' => $this->applicationName()], 'en'))
            ."\n";

        $message .= "\n<b>".e(trans('backup::notifications.exception_message_title', [], 'en')).':</b> '.e($this->event->exception->getMessage());

        foreach ($this->backupDestinationProperties() as $key => $val) {
            $message .= "\n<b>".e($key).':</b> '.e($val);
        }

        return (new TelegramMessage)
            ->to(config('backup.notifications.telegram.to'))
            ->content($message)
            ->options(['parse_mode' => 'html']);
    }
}
