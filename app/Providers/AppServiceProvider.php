<?php

namespace App\Providers;

use App\Models\Person;
use App\Observers\PersonObserver;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Spatie\Flash\Flash;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Person::observe(PersonObserver::class);

        Blade::directive('encodedjson', function ($expression) {
            return "<?php echo e(json_encode($expression)) ?>";
        });

        Flash::levels([
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'error',
        ]);
    }
}
