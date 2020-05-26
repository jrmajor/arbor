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

    public function showLoginForm()
    {
        if (! session()->has('url.intended')) {
            session()->put('url.intended', url()->previous());
        }

        return view('auth.login');
    }

    public function username()
    {
        return 'username';
    }
}
