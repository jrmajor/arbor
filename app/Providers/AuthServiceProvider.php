<?php

namespace App\Providers;

use App\Models;
use App\Policies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /** @var array<class-string<Model>, class-string> */
    protected $policies = [
        Models\Person::class => Policies\PersonPolicy::class,
        Models\Marriage::class => Policies\MarriagePolicy::class,
    ];
}
