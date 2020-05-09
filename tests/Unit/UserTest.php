<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testCorrectlyDeterminesItsAbilities()
    {
        $user = factory(User::class)->create([
            'permissions' => 0,
        ]);

        $this->assertFalse($user->canRead());
        $this->assertFalse($user->canWrite());
        $this->assertFalse($user->canDestroy());
        $this->assertFalse($user->isSuperAdmin());

        $user->permissions = 1;
        $this->assertTrue($user->canRead());
        $this->assertFalse($user->canWrite());
        $this->assertFalse($user->canDestroy());
        $this->assertFalse($user->isSuperAdmin());

        $user->permissions = 2;
        $this->assertTrue($user->canRead());
        $this->assertTrue($user->canWrite());
        $this->assertFalse($user->canDestroy());
        $this->assertFalse($user->isSuperAdmin());

        $user->permissions = 3;
        $this->assertTrue($user->canRead());
        $this->assertTrue($user->canWrite());
        $this->assertTrue($user->canDestroy());
        $this->assertFalse($user->isSuperAdmin());

        $user->permissions = 4;
        $this->assertTrue($user->canRead());
        $this->assertTrue($user->canWrite());
        $this->assertTrue($user->canDestroy());
        $this->assertTrue($user->isSuperAdmin());
    }
}
