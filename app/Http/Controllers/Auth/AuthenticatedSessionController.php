<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function App\flash;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        if (! session()->has('url.intended')) {
            session()->put('url.intended', url()->previous());
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->route('people.index');
    }

    public function destroy(Request $request): Response
    {
        auth()->guard('web')->logoutCurrentDevice();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        flash('success', 'auth.successfully_logged_out');

        return redirect()->route('people.index');
    }
}
