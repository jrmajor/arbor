<?php

use App\Application;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\AuthenticateSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/routes.php',
        commands: __DIR__ . '/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', AuthenticateSession::class);
        $middleware->appendToGroup('web', SetLocale::class);

        $middleware->redirectUsersTo('/people');
    })
    ->withExceptions(function (Exceptions $exceptions) { })
    ->create();
