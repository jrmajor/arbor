<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;

it('logs user logins events', function () {
    $user = User::factory()->create();

    Agent::shouldReceive([
        'platform' => 'OS X',
        'browser' => 'Chrome',
        'deviceType' => 'desktop',
    ]);

    Auth::login($user);

    $log = latestLog();

    expect($log->log_name)->toBe('logins');
    expect($log->description)->toBe('logged-in');
    expect($log->causer()->is($user))->toBeTrue();
    expect($log->subject)->toBeNull();

    expect($log->properties)->toHaveCount(3);
    expect($log->properties['platform'])->toBe('OS X');
    expect($log->properties['browser'])->toBe('Chrome');
    expect($log->properties['device'])->toBe('desktop');
});
