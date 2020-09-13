<?php

use App\Models\User;

it('correctly determines its abilities', function () {
    $user = User::factory()->create([
        'permissions' => 0,
    ]);

    expect($user->canRead())->toBeFalse()
        ->and($user->canWrite())->toBeFalse()
        ->and($user->canViewHistory())->toBeFalse()
        ->and($user->isSuperAdmin())->toBeFalse();

    $user->permissions = 1;
    expect($user->canRead())->toBeTrue()
        ->and($user->canWrite())->toBeFalse()
        ->and($user->canViewHistory())->toBeFalse()
        ->and($user->isSuperAdmin())->toBeFalse();

    $user->permissions = 2;
    expect($user->canRead())->toBeTrue()
        ->and($user->canWrite())->toBeTrue()
        ->and($user->canViewHistory())->toBeFalse()
        ->and($user->isSuperAdmin())->toBeFalse();

    $user->permissions = 3;
    expect($user->canRead())->toBeTrue()
        ->and($user->canWrite())->toBeTrue()
        ->and($user->canViewHistory())->toBeTrue()
        ->and($user->isSuperAdmin())->toBeFalse();

    $user->permissions = 4;
    expect($user->canRead())->toBeTrue()
        ->and($user->canWrite())->toBeTrue()
        ->and($user->canViewHistory())->toBeTrue()
        ->and($user->isSuperAdmin())->toBeTrue();
});
