<?php

namespace App\Enum;

enum SchoolCalendarDayStatusEnum : string
{
    case REGULAR = 'regular';
    case SCHEDULED = 'scheduled';
    case NO_SCHOOL = 'no_school';

    case DAY_OFF = 'day_off';

    case UNCONFIGURED = 'unconfigured';
}
