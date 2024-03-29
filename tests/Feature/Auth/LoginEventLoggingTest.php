<?php

namespace Tests\Feature\Auth;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class LoginEventLoggingTest extends TestCase
{
    #[TestDox('it logs user logins events')]
    public function testLogging(): void
    {
        $user = User::factory()->create();

        Agent::shouldReceive([
            'platform' => 'OS X',
            'browser' => 'Chrome',
            'deviceType' => 'desktop',
        ]);

        Auth::login($user);

        $log = Activity::newest();

        $this->assertSame('logins', $log->log_name);
        $this->assertSame('logged-in', $log->description);
        $this->assertSameModel($user, $log->causer);
        $this->assertNull($log->subject);

        $this->assertSame([
            'platform' => 'OS X',
            'browser' => 'Chrome',
            'device' => 'desktop',
        ], $log->properties->all());
    }
}
