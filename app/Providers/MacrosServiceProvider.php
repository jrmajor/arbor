<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class MacrosServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Carbon::macro('formatPeriodTo', static function (Carbon $to): string {
            /** @var CarbonImmutable $from */
            $from = self::this()->toImmutable();
            $to = $to->toImmutable();

            if ($from->equalTo($to)) {
                return $from->toDateString();
            }

            if (
                $from->startOfYear()->isSameDay($from)
                && $to->endOfYear()->isSameDay($to)
            ) {
                if ($from->isSameYear($to)) {
                    return (string) $from->year;
                }

                return $from->year.'-'.$to->year;
            }

            if (
                $from->startOfMonth()->isSameDay($from)
                && $to->endOfMonth()->isSameDay($to)
            ) {
                if ($from->isSameMonth($to)) {
                    return $from->format('Y-m');
                }

                return __('misc.date.between_and', [
                    'from' => $from->format('Y-m'),
                    'to' => $to->format('Y-m'),
                ]);
            }

            return __('misc.date.between_and', [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ]);
        });

        Str::macro('formatBiography', function (?string $biography): ?string {
            if ($biography === null) {
                return null;
            }

            return (string) Str::of($biography)
                ->trim()
                ->replace(["\r\n", "\r"], "\n")
                ->pipe('e')
                ->prepend('<p>')
                ->append('</p>')
                ->replace("\n\n", "</p>\n<p>");
        });

        Arr::macro('trim', function (array $array): array {
            foreach ($array as $key => $value) {
                $array[$key] = match (gettype($value)) {
                    'array' => self::trim($value),
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
