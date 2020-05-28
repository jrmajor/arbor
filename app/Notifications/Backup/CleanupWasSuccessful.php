<?php

namespace App\Notifications\Backup;

use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\CleanupWasSuccessful as CleanupWasSuccessfulEvent;
use Spatie\Backup\Notifications\BaseNotification;

class CleanupWasSuccessful extends BaseNotification
{
    /** @var \Spatie\Backup\Events\CleanupWasSuccessful */
    protected $event;

    public function __construct(CleanupWasSuccessfulEvent $event)
    {
        $this->event = $event;
    }

    public function toTelegram(): TelegramMessage
    {
        return (new TelegramMessage)
            ->to(config('backup.notifications.telegram.to'))
            ->content('*'.trans('backup::notifications.cleanup_successful_subject', ['application_name' => $this->applicationName()], 'en').'*');
    }
}
