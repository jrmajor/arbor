<?php

namespace App\View\Components;

use Carbon\Carbon;
use Illuminate\View\Component;

class DateRangePicker extends Component
{
    protected ?Carbon $initialFrom = null;

    protected ?Carbon $initialTo = null;

    protected bool $hasErrors = false;

    public function __construct(
        public string $name,
        public string $label,
        Carbon|string|null $initialFrom,
        Carbon|string|null $initialTo,
    ) {
        if (session()->get('errors')?->has(["{$name}_from", "{$name}_to"])) {
            $this->hasErrors = false;

            return;
        }

        $this->initialFrom = is_string($initialFrom)
            ? Carbon::create($initialFrom)
            : $initialFrom;

        $this->initialTo = is_string($initialTo)
            ? Carbon::create($initialTo)
            : $initialTo;
    }

    public function pickerData(): array
    {
        return [
            'simple' => $this->initialSimplePickerValue(),
            'from' => $this->initialFrom?->format('Y-m-d'),
            'to' => $this->initialTo?->format('Y-m-d'),
            'advancedPicker' => $this->hasErrors || ! $this->simplePickerCanBeUsed(),
        ];
    }

    protected function simplePickerCanBeUsed(): bool
    {
        $from = $this->initialFrom;
        $to = $this->initialTo;

        if (! $from || ! $to) {
            return true;
        }

        if ($from->equalTo($to)) {
            return true;
        }

        $to = $to->endOfDay();

        if (
            $from->copy()->startOfYear()->equalTo($from)
            && $to->copy()->endOfYear()->equalTo($to)
        ) {
            return $from->year === $to->year;
        }

        if (
            $from->copy()->startOfMonth()->equalTo($from)
            && $to->copy()->endOfMonth()->equalTo($to)
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

        $to = $to->endOfDay();

        if (
            $from->copy()->startOfYear()->equalTo($from)
            && $to->copy()->endOfYear()->equalTo($to)
        ) {
            if ($from->year === $to->year) {
                return $from->format('Y');
            }

            return null;
        }

        if (
            $from->copy()->startOfMonth()->equalTo($from)
            && $to->copy()->endOfMonth()->equalTo($to)
            && $from->year === $to->year
            && $from->month === $to->month
        ) {
            return $from->format('Y-m');
        }

        return null;
    }

    public function render()
    {
        return view('components.date-range-picker');
    }
}
