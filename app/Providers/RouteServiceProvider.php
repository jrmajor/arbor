<?php

namespace App\Providers;

use App\Marriage;
use App\Person;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';

    public const HOME = '/people';

    public function map()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/routes.php'));
    }

    public function boot()
    {
        parent::boot();

        Route::bind('trashedPerson', fn ($id) => Person::onlyTrashed()->findOrFail($id));

        Route::bind('anyPerson', fn ($id) => Person::withTrashed()->findOrFail($id));

        Route::bind('trashedMarriage', fn ($id) => Marriage::onlyTrashed()->findOrFail($id));

        Route::bind('anyMarriage', fn ($id) => Marriage::withTrashed()->findOrFail($id));
    }
}
