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

    private function getAgent()
    {
        if (Agent::isDesktop()) {
            $device = 'desktop';
        } elseif (Agent::isPhone()) {
            $device = 'phone';
        } else {
            $device = null;
        }

        return [
            'platform' => Agent::platform(),
            'browser' => Agent::browser(),
            'device' => $device,
        ];
    }
}
