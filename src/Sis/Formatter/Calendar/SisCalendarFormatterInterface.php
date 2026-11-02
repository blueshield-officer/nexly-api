<?php

namespace App\Sis\Formatter\Calendar;

interface SisCalendarFormatterInterface
{
    public function format(array | null $calendars) : array | null;
}
