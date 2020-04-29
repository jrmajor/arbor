<?php

if (! function_exists('format_date')) {
    function format_date($date)
    {
        $year = substr($date, 0, 4);
        $month = substr($date, -5, 2);
        $day = substr($date, -2, 2);

        if($month == '00')
            return $year;
        elseif($day == '00')
            return $year.'-'.$month;
        else
            return $date;
    }
}

if (! function_exists('roman')) {
    function roman($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
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
    function faker() {
        return app('Faker\Generator');
    }
}
