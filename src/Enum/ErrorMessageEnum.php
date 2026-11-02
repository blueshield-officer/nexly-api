<?php

namespace App\Enum;

enum ErrorMessageEnum : string
{
    case INVALID_GOOGLE_TOKEN = 'Invalid Google Token';
    case INVALID_REFRESH_TOKEN = 'Invalid Refresh Token';
    case DISTRICT_NOT_FOUND = 'District was not found';
    case SCHOOL_NOT_FOUND = 'School was not found';
    case SCHOOL_SCHEDULE_NOT_FOUND = 'Schedule was not found for this school';
    case SCHOOL_DAY_CYCLE_NOT_FOUND = 'Day cycle  was not found for this school';
}
