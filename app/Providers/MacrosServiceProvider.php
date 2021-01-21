<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class MacrosServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Carbon::macro('formatPeriodTo', static function (Carbon $to): string {
            $from = self::this()->toImmutable();
            $to = $to->toImmutable();

            if ($from->equalTo($to)) {
                return $from->toDateString();
            }

            $to = $to->endOfDay();

            if (
                $from->startOfYear()->equalTo($from)
                && $to->endOfYear()->equalTo($to)
            ) {
                if ($from->year === $to->year) {
                    return (string) $from->year;
                } else {
                    return $from->year.'-'.$to->year;
                }
            }

            if (
                $from->startOfMonth()->equalTo($from)
                && $to->endOfMonth()->equalTo($to)
            ) {
                if ($from->year === $to->year && $from->month === $to->month) {
                    return $from->year.'-'.$from->format('m');
                } else {
                    return __('misc.date.between').' '.$from->year.'-'.$from->format('m')
                        .' '.__('misc.date.and').' '.$to->year.'-'.$to->format('m');
                }
            }

            return __('misc.date.between').' '.$from->toDateString().' '.__('misc.date.and').' '.$to->toDateString();
        });

        Stringable::macro('e', function (): Stringable {
            return new Stringable(e($this->value));
        });

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
                    $array[$key] = self::trim($value);
                } elseif (is_string($value)) {
                    $array[$key] = trim($value);
                }

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
