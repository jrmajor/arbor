<?php

if (! function_exists('format_date_from_period')) {
    function format_date_from_period($from, $to)
    {
        if ($from->equalTo($to)) {
            return $from->toDateString();
        }

        $to = $to->endOfDay();

        if (
            $from->copy()->startOfYear()->equalTo($from)
            && $to->copy()->endOfYear()->equalTo($to)
        ) {
            if ($from->year == $to->year) {
                return $from->year;
            } else {
                return $from->year.'-'.$to->year;
            }
        }

        if (
            $from->copy()->startOfMonth()->equalTo($from)
            && $to->copy()->endOfMonth()->equalTo($to)
        ) {
            if ($from->year == $to->year && $from->month == $to->month) {
                return $from->year.'-'.$from->format('m');
            } else {
                return __('misc.date.between').' '.$from->year.'-'.$from->format('m')
                    .' '.__('misc.date.and').' '.$to->year.'-'.$to->format('m');
            }
        }

        return __('misc.date.between').' '.$from->toDateString().' '.__('misc.date.and').' '.$to->toDateString();
    }
}

if (! function_exists('roman')) {
    function roman($number)
    {
        $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }

        return $returnValue;
    }
}

if (! function_exists('faker')) {
    function faker()
    {
        return app('Faker\Generator');
    }
}
