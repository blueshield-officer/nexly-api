<?php

namespace App\Sis\Formatter\Calendar;

class DemoCalendarFormatter implements SisCalendarFormatterInterface
{
    public function format(array | null $calendars) : array | null
    {
        return $calendars;
    }
}
