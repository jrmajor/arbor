<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class UserTest extends TestCase
{
    #[TestDox('it correctly determines its abilities')]
    public function testAbilities(): void
    {
        $user = User::factory()->createOne(['permissions' => 0]);

        $this->assertFalse($user->canRead());
        $this->assertFalse($user->canWrite());
        $this->assertFalse($user->canViewHistory());
        $this->assertFalse($user->isSuperAdmin());

        $user->permissions = 1;
        $this->assertTrue($user->canRead());
        $this->assertFalse($user->canWrite());
        $this->assertFalse($user->canViewHistory());
        $this->assertFalse($user->isSuperAdmin());

        $user->permissions = 2;
        $this->assertTrue($user->canRead());
        $this->assertTrue($user->canWrite());
        $this->assertFalse($user->canViewHistory());
        $this->assertFalse($user->isSuperAdmin());

        $user->permissions = 3;
        $this->assertTrue($user->canRead());
        $this->assertTrue($user->canWrite());
        $this->assertTrue($user->canViewHistory());
        $this->assertFalse($user->isSuperAdmin());

        $user->permissions = 4;
        $this->assertTrue($user->canRead());
        $this->assertTrue($user->canWrite());
        $this->assertTrue($user->canViewHistory());
        $this->assertTrue($user->isSuperAdmin());
    }
}
