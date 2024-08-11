<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Assert;

class MacrosServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerCarbonMacros();

        if (
            $this->app->runningUnitTests()
            || isset($GLOBALS['__phpstanAutoloadFunctions'])
        ) {
            $this->registerTestingMacros();
        }
    }

    private function registerCarbonMacros(): void
    {
        Carbon::macro('formatPeriodTo', static function (Carbon $to): string {
            /** @phpstan-ignore staticMethod.notFound */
            $from = self::this()->toImmutable();
            assert($from instanceof CarbonImmutable);

            $to = $to->toImmutable();

            if ($from->equalTo($to)) {
                return $from->toDateString();
            }

            if ($from->startOfYear()->isSameDay($from) && $to->endOfYear()->isSameDay($to)) {
                return $from->isSameYear($to) ? (string) $from->year : "{$from->year}-{$to->year}";
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

    private function registerTestingMacros(): void
    {
        TestResponse::macro('assertInertiaComponent', function (string $component): TestResponse {
            return $this
                ->assertOk()
                ->assertInertia(fn (AssertableInertia $page) => $page->component($component));
        });

        TestResponse::macro('assertInertiaResponse', function (
            int $status, array $props, string $component,
        ): TestResponse {
            return $this
                ->assertStatus($status)
                ->assertInertia(function (AssertableInertia $page) use ($component, $props) {
                    $page->component($component)->assertProps($props);
                });
        });

        TestResponse::macro('assertInertiaOk', function (
            array $props, string $component,
        ): TestResponse {
            return $this->assertInertiaResponse(200, $props, $component);
        });

        AssertableJson::macro('assertProps', function (array $expected): AssertableJson {
            $props = $this->toArray()['props'];
            $props = Arr::except($props, [
                'errors',
                'appName',
                'currentYear',
                'currentLocale',
                'fallbackLocale',
                'availableLocales',
                'flash',
                'activeRoute',
                'user',
            ]);
            Assert::assertSame($expected, $props);

            return $this;
        });
    }
}
