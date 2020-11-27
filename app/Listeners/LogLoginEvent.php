<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Jenssegers\Agent\Facades\Agent;

class LogLoginEvent
{
    public function handle(Login $event)
    {
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
