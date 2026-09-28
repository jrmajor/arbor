<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

use function App\flash;

class PasswordResetLinkController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink(
            $request->only('email'),
            function (User $user, string $token) {
                try {
                    $user->sendPasswordResetNotification($token);
                } catch (Throwable $e) {
                    report($e);

                    // the broker throttles based on the stored token
                    // remove it to let the user retry right away
                    Password::deleteToken($user);

                    return 'passwords.failed';
                }
            },
        );

        if ($status === Password::RESET_LINK_SENT) {
            flash('success', $status);

            return redirect()->route('people.index');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
