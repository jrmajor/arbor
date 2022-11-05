<?php

namespace App\Providers;

use App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Spatie\Flash\Flash;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'marriage' => Models\Marriage::class,
            'person' => Models\Person::class,
            'user' => Models\User::class,
        ]);

        $shouldBeStrict = ! $this->app->environment('production');

        Model::preventLazyLoading($shouldBeStrict);
        // Model::preventSilentlyDiscardingAttributes($shouldBeStrict);
        Model::preventAccessingMissingAttributes($shouldBeStrict);

        Flash::levels([
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'error',
        ]);
    }
}
