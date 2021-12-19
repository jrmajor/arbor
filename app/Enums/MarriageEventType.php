<?php

namespace App\Enums;

enum MarriageEventType: string
{
    case Marriage = 'marriage';
    case ChurchMarriage = 'church_marriage';
    case CivilMarriage = 'civil_marriage';
    case ConcordatMarriage = 'concordat_marriage';
}
