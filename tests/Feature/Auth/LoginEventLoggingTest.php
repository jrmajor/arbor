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

    assertEquals('logins', $log->log_name);
    assertEquals('logged-in', $log->description);
    assertTrue($user->is($log->causer));
    assertNull($log->subject);

    assertCount(3, $log->properties);
    assertEquals('OS X', $log->properties['platform']);
    assertEquals('Chrome', $log->properties['browser']);
    assertEquals('desktop', $log->properties['device']);
});
