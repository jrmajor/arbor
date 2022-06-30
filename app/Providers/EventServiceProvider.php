<?php

namespace App\Providers;

use App\Listeners\LogLoginEvent;
use App\Models\Observers\PersonObserver;
use App\Models\Person;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /** @var array<class-string, list<class-string>> */
    protected $listen = [
        Login::class => [LogLoginEvent::class],
    ];

    public function boot(): void
    {
        Person::observe(PersonObserver::class);
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
