<?php

namespace App\Sis\Formatter\Schedule;

class DemoScheduleFormatter implements SisScheduleFormatterInterface
{
    public function format(array $schedules) : array
    {
        $formatted = [];
        foreach ($schedules as $externalId => $schedule) {
            $formatted[$externalId] = [
                'periods' => $schedule['periods'],
            ];
        }

        return $formatted;
    }
}
