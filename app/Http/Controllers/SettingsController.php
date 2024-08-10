<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

use function App\flash;

class SettingsController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Settings');
    }

    public function updateEmail(Request $request): void
    {
        $request->validate([
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)
                    ->ignore($request->user()->id),
            ],
        ]);

        $request->user()->update(['email' => $request->string('email')])
            ? flash('success', 'settings.alerts.changes_have_been_saved')
            : flash('error', 'misc.an_unknown_error_occurred');
    }

    public function updatePassword(Request $request): void
    {
        $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);

        $request->user()->update(['password' => $request->string('password')])
            ? flash('success', 'settings.alerts.password_has_been_changed')
            : flash('error', 'misc.an_unknown_error_occurred');
    }

    public function logoutOtherDevices(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required']]);
        $password = $request->string('password');

        if (! Hash::check($password, $request->user()->password)) {
            return back()->withErrors([
                'password' => __('settings.wrong_password'),
            ]);
        }

        Auth::guard()->logoutOtherDevices($password);

        flash('success', 'settings.alerts.logged_out');

        return back();
    }
}
