<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;

use function Tests\latestLog;

it('logs user logins events', function () {
    $user = User::factory()->create();

    Agent::shouldReceive([
        'platform' => 'OS X',
        'browser' => 'Chrome',
        'deviceType' => 'desktop',
    ]);

    Auth::login($user);

    $log = latestLog();

    expect($log)
        ->log_name->toBe('logins')
        ->description->toBe('logged-in')
        ->causer->toBeModel($user)
        ->subject->toBeNull();

    expect($log->properties->all())->toBe([
        'device' => 'desktop',
        'browser' => 'Chrome',
        'platform' => 'OS X',
    ]);
});
