<?php

use App\Models\User;

it('logs user logins events', function () {
    $user = User::factory()->create();

    Agent::shouldReceive([
        'isDesktop' => true,
        'isPhone' => false,
        'platform' => 'OS X',
        'browser' => 'Chrome',
    ]);

    Auth::login($user);

    $log = latestLog();

    expect($log->log_name)->toBe('logins');
    expect($log->description)->toBe('logged-in');
    expect($user->is($log->causer))->toBeTrue();
    expect($log->subject)->toBeNull();

    expect($log->properties)->toHaveCount(3);
    expect($log->properties['platform'])->toBe('OS X');
    expect($log->properties['browser'])->toBe('Chrome');
    expect($log->properties['device'])->toBe('desktop');
});
