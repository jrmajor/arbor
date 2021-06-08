<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class MacrosServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerCarbonMacros();
        $this->registerTrimArrayMacro();

        Blade::directive('encodedjson', function ($expression) {
            return "<?php echo e(json_encode({$expression})) ?>";
        });
    }

    public function registerCarbonMacros()
    {
        Carbon::macro('formatPeriodTo', static function (Carbon $to): string {
            /** @var CarbonImmutable $from */
            $from = self::this()->toImmutable();
            $to = $to->toImmutable();

            if ($from->equalTo($to)) {
                return $from->toDateString();
            }

            if ($from->startOfYear()->isSameDay($from) && $to->endOfYear()->isSameDay($to)) {
                return $from->isSameYear($to) ? (string) $from->year : $from->year.'-'.$to->year;
            }

            if ($from->startOfMonth()->isSameDay($from) && $to->endOfMonth()->isSameDay($to)) {
                return $from->isSameMonth($to)
                    ? $from->format('Y-m')
                    : __('misc.date.between_and', [
                        'from' => $from->format('Y-m'),
                        'to' => $to->format('Y-m'),
                    ]);
            }

            return __('misc.date.between_and', [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ]);
        });
    }

    public function registerTrimArrayMacro()
    {
        Arr::macro('trim', function (array|Collection $array): array|Collection {
            foreach ($array as $key => $value) {
                $array[$key] = match (get_debug_type($value)) {
                    'array', Collection::class => self::trim($value),
                    'string' => trim($value),
                    default => $value,
                };

                if (blank($array[$key])) {
                    unset($array[$key]);
                }
            }

            return $array;
        });

        Collection::macro('trim', function (): Collection {
            return new static(Arr::trim($this->items));
        });
    }
}
