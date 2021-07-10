<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;

use function App\Services\flash;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        if (! session()->has('url.intended')) {
            session()->put('url.intended', url()->previous());
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->route('people.index');
    }

    public function destroy(Request $request)
    {
        auth()->guard('web')->logoutCurrentDevice();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        flash('success', 'auth.successfully_logged_out');

        return redirect()->route('people.index');
    }
}
