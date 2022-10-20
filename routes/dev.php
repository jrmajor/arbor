<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

Route::get('dev/notify/reset-password', function (Request $request) {
    $user = $request->has('user')
        ? User::find($request['user'])
        : User::first();

    if (! $user) {
        return 'User does not exist.';
    }

    $status = Password::sendResetLink($user->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? "Sent ResetPassword notification to {$user->email}."
        : "Failed to send ResetPassword notification to {$user->email}: {$status}.";
});
