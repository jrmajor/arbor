<?php

namespace Tests\Feature\Dashboard;

use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class UsersTest extends TestCase
{
    #[TestDox('guests are asked to log in when attempting to view users page')]
    public function testGuest(): void
    {
        $this->get('dashboard/users')->assertFound()->assertRedirect('login');
    }

    #[TestDox('users without permissions cannot view users page')]
    public function testPermissions(): void
    {
        $this->withPermissions(3)->get('dashboard/users')->assertForbidden();
    }

    #[TestDox('users with permissions can view users page')]
    public function testOk(): void
    {
        $this->withPermissions(4)->get('dashboard/users')->assertOk();
    }
}
