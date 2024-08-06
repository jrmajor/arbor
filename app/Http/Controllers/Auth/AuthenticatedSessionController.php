<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use function App\flash;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        if (! session()->has('url.intended')) {
            session()->put('url.intended', url()->previous());
        }

        return Inertia::render('Auth/Login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('people.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        auth()->guard('web')->logoutCurrentDevice();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        flash('success', 'auth.successfully_logged_out');

        return redirect()->route('people.index');
    }
}
