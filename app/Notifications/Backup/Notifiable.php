<?php

namespace App\Notifications\Backup;

use Illuminate\Notifications\Notifiable as NotifiableTrait;

class Notifiable
{
    use NotifiableTrait;

    public function routeNotificationForTelegram()
    {
        return config('backup.notifications.telegram.to');
    }

    public function getKey()
    {
        return 1;
    }
}
