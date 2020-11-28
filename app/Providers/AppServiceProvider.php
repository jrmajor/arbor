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

        Stringable::macro('e', fn () => new Stringable(e($this->value)));

        Str::macro('formatBiography', function (?string $biography): ?string {
            if ($biography === null) {
                return null;
            }

            return (string) Str::of($biography)
                ->trim()
                ->replace(["\r\n", "\r"], "\n")
                ->e()
                ->prepend('<p>')
                ->append('</p>')
                ->replace("\n\n", "</p>\n<p>");
        });

        Arr::macro('trim', function (array $array): array {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = Arr::trim($value);
                    if (blank($array[$key])) {
                        unset($array[$key]);
                    }
                } else {
                    $array[$key] = trim($value);
                    if (blank($array[$key])) {
                        unset($array[$key]);
                    }
                }
            }

            return $array;
        });

        Collection::macro('trim', function (): Collection {
            return new static(Arr::trim($this->items));
        });

        Flash::levels([
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'error',
        ]);
    }
}
