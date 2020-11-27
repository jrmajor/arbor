<?php

namespace App\View\Components;

use Carbon\Carbon;
use Illuminate\View\Component;

class DateTuplePicker extends Component
{
    public $name;

    public $label;

    public $initialFrom;

    public $initialTo;

    public function __construct($name, $label, $initialFrom, $initialTo)
    {
        if ($initialFrom != null && ! $initialFrom instanceof Carbon) {
            $initialFrom = Carbon::create($initialFrom);
        }

        if ($initialTo != null && ! $initialTo instanceof Carbon) {
            $initialTo = Carbon::create($initialTo);
        }

        $this->name = $name;
        $this->label = $label;
        $this->initialFrom = $initialFrom;
        $this->initialTo = $initialTo;
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

            return null;
        }
    }

    public function render()
    {
        return view('components.date-tuple-picker');
    }
}
