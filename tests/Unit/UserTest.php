<?php

use App\User;

it('correctly determines its abilities', function () {
    $user = factory(User::class)->create([
        'permissions' => 0,
    ]);

    assertFalse($user->canRead());
    assertFalse($user->canWrite());
    assertFalse($user->canDestroy());
    assertFalse($user->isSuperAdmin());

    $user->permissions = 1;
    assertTrue($user->canRead());
    assertFalse($user->canWrite());
    assertFalse($user->canDestroy());
    assertFalse($user->isSuperAdmin());

    $user->permissions = 2;
    assertTrue($user->canRead());
    assertTrue($user->canWrite());
    assertFalse($user->canDestroy());
    assertFalse($user->isSuperAdmin());

    $user->permissions = 3;
    assertTrue($user->canRead());
    assertTrue($user->canWrite());
    assertTrue($user->canDestroy());
    assertFalse($user->isSuperAdmin());

    $user->permissions = 4;
    assertTrue($user->canRead());
    assertTrue($user->canWrite());
    assertTrue($user->canDestroy());
    assertTrue($user->isSuperAdmin());
});
