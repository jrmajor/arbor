<?php

use App\Models\User;

it('correctly determines its abilities', function () {
    $user = User::factory()->create([
        'permissions' => 0,
    ]);

    expect($user)
        ->canRead()->toBeFalse()
        ->canWrite()->toBeFalse()
        ->canViewHistory()->toBeFalse()
        ->isSuperAdmin()->toBeFalse();

    $user->permissions = 1;
    expect($user)
        ->canRead()->toBeTrue()
        ->canWrite()->toBeFalse()
        ->canViewHistory()->toBeFalse()
        ->isSuperAdmin()->toBeFalse();

    $user->permissions = 2;
    expect($user)
        ->canRead()->toBeTrue()
        ->canWrite()->toBeTrue()
        ->canViewHistory()->toBeFalse()
        ->isSuperAdmin()->toBeFalse();

    $user->permissions = 3;
    expect($user)
        ->canRead()->toBeTrue()
        ->canWrite()->toBeTrue()
        ->canViewHistory()->toBeTrue()
        ->isSuperAdmin()->toBeFalse();

    $user->permissions = 4;
    expect($user)
        ->canRead()->toBeTrue()
        ->canWrite()->toBeTrue()
        ->canViewHistory()->toBeTrue()
        ->isSuperAdmin()->toBeTrue();
});
