<?php

namespace App\Sis\Formatter\Schedule;

interface SisScheduleFormatterInterface
{
    public function format(array $schedules) : array;

}
