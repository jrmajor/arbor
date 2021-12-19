<?php

namespace App\Enums;

enum Sex: string
{
    case Male = 'xy';
    case Female = 'xx';

    public function valueForFaker(): string
    {
        return match ($this) {
            self::Male => 'male',
            self::Female => 'female',
        };
    }
}
