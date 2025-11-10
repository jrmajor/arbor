<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;

class LogLoginEvent
{
    public function handle(Login $event): void
    {
        assert($event->user instanceof User);

        activity('logins')
            ->causedBy($event->user)
            ->log('logged-in');
    }
}
