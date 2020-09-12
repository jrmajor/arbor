<?php

use App\User;

it('correctly determines its abilities', function () {
    $user = User::factory()->create([
        'permissions' => 0,
    ]);

    assertFalse($user->canRead());
    assertFalse($user->canWrite());
    assertFalse($user->canViewHistory());
    assertFalse($user->isSuperAdmin());

    $user->permissions = 1;
    assertTrue($user->canRead());
    assertFalse($user->canWrite());
    assertFalse($user->canViewHistory());
    assertFalse($user->isSuperAdmin());

    $user->permissions = 2;
    assertTrue($user->canRead());
    assertTrue($user->canWrite());
    assertFalse($user->canViewHistory());
    assertFalse($user->isSuperAdmin());

    $user->permissions = 3;
    assertTrue($user->canRead());
    assertTrue($user->canWrite());
    assertTrue($user->canViewHistory());
    assertFalse($user->isSuperAdmin());

    $user->permissions = 4;
    assertTrue($user->canRead());
    assertTrue($user->canWrite());
    assertTrue($user->canViewHistory());
    assertTrue($user->isSuperAdmin());
});
