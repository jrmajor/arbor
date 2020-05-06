<?php

namespace App\Providers;

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

        Route::bind('maybe_trashed_person', fn ($id) =>
            Person::withTrashed()->findOrFail($id)
        );
    }
}
