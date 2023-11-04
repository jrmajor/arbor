<?php

namespace App\View\Components;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Js;
use Illuminate\View\Component;

class DateRangePicker extends Component
{
    protected ?CarbonImmutable $initialFrom = null;

    protected ?CarbonImmutable $initialTo = null;

    protected bool $hasErrors = false;

    public function __construct(
        public string $name,
        public string $label,
        CarbonInterface|string|null $initialFrom,
        CarbonInterface|string|null $initialTo,
    ) {
        if (session()->get('errors')?->has(["{$name}_from", "{$name}_to"])) {
            $this->hasErrors = true;

            return;
        }

        $this->initialFrom = is_string($initialFrom)
            ? CarbonImmutable::parse($initialFrom)
            : $initialFrom?->toImmutable();

        $this->initialTo = is_string($initialTo)
            ? CarbonImmutable::parse($initialTo)
            : $initialTo?->toImmutable();
    }

    public function pickerData(): Js
    {
        return new Js([
            'simple' => $this->initialSimplePickerValue(),
            'from' => $this->initialFrom?->format('Y-m-d'),
            'to' => $this->initialTo?->format('Y-m-d'),
            'advancedPicker' => $this->hasErrors || ! $this->simplePickerCanBeUsed(),
        ]);
    }

    protected function simplePickerCanBeUsed(): bool
    {
        $from = $this->initialFrom;
        $to = $this->initialTo;

        if (! $from || ! $to || $from->equalTo($to)) {
            return true;
        }

        if (
            $from->startOfYear()->isSameDay($from)
            && $to->endOfYear()->isSameDay($to)
        ) {
            return $from->year === $to->year;
        }

        if (
            $from->startOfMonth()->isSameDay($from)
            && $to->endOfMonth()->isSameDay($to)
        ) {
            return $from->year === $to->year && $from->month === $to->month;
        }

        return false;
    }

    protected function initialSimplePickerValue(): ?string
    {
        $from = $this->initialFrom;
        $to = $this->initialTo;

        if (! $from || ! $to) {
            return null;
        }

        if ($from->equalTo($to)) {
            return $from->format('Y-m-d');
        }

        if (
            $from->startOfYear()->isSameDay($from)
            && $to->endOfYear()->isSameDay($to)
            && $from->isSameYear($to)
        ) {
            return $from->format('Y');
        }

        if (
            $from->startOfMonth()->isSameDay($from)
            && $to->endOfMonth()->isSameDay($to)
            && $from->isSameMonth($to)
        ) {
            return $from->format('Y-m');
        }

        return null;
    }

    public function render(): View
    {
        return view('components.date-range-picker');
    }
}
