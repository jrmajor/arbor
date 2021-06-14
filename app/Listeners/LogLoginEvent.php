<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Jenssegers\Agent\Facades\Agent;
use TypeError;

class LogLoginEvent
{
    public function handle(Login $event)
    {
        if (! $event->user instanceof User) {
            throw new TypeError('$event->user should be instance of App\\Models\\User.');
        }

        activity('logins')
            ->causedBy($event->user)
            ->withProperties($this->getAgent())
            ->log('logged-in');
    }

    private function getAgent(): array
    {
        return [
            'platform' => Agent::platform(),
            'browser' => Agent::browser(),
            'device' => Agent::deviceType(),
        ];
    }
}
