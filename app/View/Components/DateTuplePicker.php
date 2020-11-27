<?php

namespace App\View\Components;

use Carbon\Carbon;
use Illuminate\View\Component;

class DateTuplePicker extends Component
{
    public ?Carbon $initialFrom;

    public ?Carbon $initialTo;

    public function __construct(
        public string $name,
        public string $label,
        Carbon|string|null $initialFrom,
        Carbon|string|null $initialTo
    ) {
        $this->initialFrom = is_string($initialFrom)
            ? Carbon::create($initialFrom)
            : $initialFrom;

        $this->initialTo = is_string($initialTo)
            ? Carbon::create($initialTo)
            : $initialTo;
    }

    public function simplePickerCanBeUsed(): bool
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
            if ($from->year === $to->year) {
                return true;
            }

            return false;
        }

        if (
            $from->copy()->startOfMonth()->equalTo($from)
            && $to->copy()->endOfMonth()->equalTo($to)
        ) {
            if ($from->year === $to->year && $from->month === $to->month) {
                return true;
            }

            return false;
        }

        return false;
    }

    public function initialSimplePickerValue(): ?string
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
        ) {
            if ($from->year === $to->year && $from->month === $to->month) {
                return $from->format('Y-m');
            }
        }

        return null;
    }

    public function render()
    {
        return view('components.date-tuple-picker');
    }
}
