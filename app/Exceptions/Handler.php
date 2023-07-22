<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Psr\Log\LogLevel;
use Throwable;

class Handler extends ExceptionHandler
{
    /** @var array<class-string<Throwable>, LogLevel::*> */
    protected $levels = [];

    /** @var list<class-string<Throwable>> */
    protected $dontReport = [];

    /** @var list<string> */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) { });
    }
}
