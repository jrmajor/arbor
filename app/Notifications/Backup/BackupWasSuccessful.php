<?php

namespace App\Notifications\Backup;

use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Events\BackupWasSuccessful as BackupWasSuccessfulEvent;

class BackupWasSuccessful extends BaseNotification
{
    /** @var \Spatie\Backup\Events\BackupWasSuccessful */
    protected $event;

    public function __construct(BackupWasSuccessfulEvent $event)
    {
        $this->event = $event;
    }

    public function toTelegram(): TelegramMessage
    {
        $message =
            e(trans('backup::notifications.backup_successful_subject_title', [], 'en'))
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
