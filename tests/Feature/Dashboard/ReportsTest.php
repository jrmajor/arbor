<?php

namespace Tests\Feature\Dashboard;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ReportsTest extends TestCase
{
    #[TestDox('guests are asked to log in when attempting to view reports')]
    public function testGuest(): void
    {
        $this->get('dashboard/reports')->assertFound()->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view reports')]
    public function testPermissions(): void
    {
        $this->withPermissions(3)->get('dashboard/reports')->assertForbidden();
    }

    #[TestDox('users with permissions can view reports')]
    public function testOk(): void
    {
        $this->withPermissions(4)->get('dashboard/reports')->assertInertiaComponent('Dashboard/Reports');
    }
}
