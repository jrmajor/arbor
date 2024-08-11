<?php

namespace Tests\Feature\Dashboard;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ActivityLogTest extends TestCase
{
    #[TestDox('guests are asked to log in when attempting to view activity log')]
    public function testGuest(): void
    {
        $this->get('dashboard/activity-log')->assertFound()->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view activity log')]
    public function testPermissions(): void
    {
        $this->withPermissions(3)->get('dashboard/activity-log')->assertForbidden();
    }

    #[TestDox('users with permissions can view activity log')]
    public function testOk(): void
    {
        $this->withPermissions(4)->get('dashboard/activity-log')->assertInertiaComponent('Dashboard/ActivityLog');
    }
}
