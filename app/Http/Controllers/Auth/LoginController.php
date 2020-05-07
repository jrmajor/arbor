<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        if (! session()->has('url.intended')) {
            session()->put('url.intended', url()->previous());
        }

        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        activity('logins')
            ->causedBy($user)
            ->withProperties($this->getAgent())
            ->log('logged-in');
    }

    private function getAgent()
    {
        if (Agent::isDesktop()) {
            $device = 'desktop';
        } elseif (Agent::isPhone()) {
            $device = 'phone';
        } else {
            $device = null;
        }

        return [
            'platform' => Agent::platform(),
            'browser' => Agent::browser(),
            'device' => $device,
        ];
    }

    public function username()
    {
        return 'username';
    }
}
