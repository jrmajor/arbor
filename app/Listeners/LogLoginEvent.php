<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Jenssegers\Agent\Facades\Agent;
use Psl\Type;

class LogLoginEvent
{
    public function handle(Login $event): void
    {
        $user = Type\instance_of(User::class)->coerce($event->user);

        activity('logins')
            ->causedBy($user)
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
