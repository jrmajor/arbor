<?php

namespace App\Providers;

use App\Listeners\LogLoginEvent;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            LogLoginEvent::class,
        ],
    ];
}
