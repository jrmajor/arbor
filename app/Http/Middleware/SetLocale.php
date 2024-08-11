<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale')
            ?? $request->getPreferredLanguage(config('app.available_locales'));

        $request->setLocale($locale);
        app()->setLocale($locale);

        return $next($request);
    }
}
