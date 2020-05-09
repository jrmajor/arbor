<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            app()->setLocale(Session::get('locale'));

            return $next($request);
        }

        app()->setLocale(
            $request->getPreferredLanguage(config('app.available_locales'))
        );

        return $next($request);
    }
}
