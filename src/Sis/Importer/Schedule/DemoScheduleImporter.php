<?php

namespace App\Sis\Importer\Schedule;

use App\Sis\Formatter\Schedule\DemoScheduleFormatter;

class DemoScheduleImporter extends AbstractScheduleImporter
{
    public function __construct(
        private readonly DemoScheduleFormatter $formatter
    ){}
    public function import(): array
    {
        $raw = [
            'a' => [
                'name' => 'A',
                'description' => 'Regular Schedule (8:00–3:00, with Homeroom)',
                'periods' => [
                    ['name' => 'Homeroom', 'position' => 0, 'startTime' => '08:00:00', 'endTime' => '08:20:00'],
                    ['name' => 'Period 1', 'position' => 1, 'startTime' => '08:25:00', 'endTime' => '09:15:00'],
                    ['name' => 'Period 2', 'position' => 2, 'startTime' => '09:20:00', 'endTime' => '10:10:00'],
                    ['name' => 'Period 3', 'position' => 3, 'startTime' => '10:15:00', 'endTime' => '11:05:00'],
                    ['name' => 'Period 4', 'position' => 4, 'startTime' => '11:10:00', 'endTime' => '12:00:00'],
                    ['name' => 'Period 5', 'position' => 5, 'startTime' => '12:05:00', 'endTime' => '12:55:00'],
                    ['name' => 'Period 6', 'position' => 6, 'startTime' => '13:00:00', 'endTime' => '13:50:00'],
                    ['name' => 'Period 7', 'position' => 7, 'startTime' => '13:55:00', 'endTime' => '15:00:00'],
                ]
            ],

            'b' => [
                'name' => 'B',
                'description' => 'Early Dismissal (shorter periods)',
                'periods' => [
                    ['name' => 'Period 1', 'position' => 0, 'startTime' => '08:00:00', 'endTime' => '08:30:00'],
                    ['name' => 'Period 2', 'position' => 1, 'startTime' => '08:35:00', 'endTime' => '09:05:00'],
                    ['name' => 'Period 3', 'position' => 2, 'startTime' => '09:10:00', 'endTime' => '09:40:00'],
                    ['name' => 'Period 4', 'position' => 3, 'startTime' => '09:45:00', 'endTime' => '10:15:00'],
                    ['name' => 'Period 5', 'position' => 4, 'startTime' => '10:20:00', 'endTime' => '10:50:00'],
                    ['name' => 'Period 6', 'position' => 5, 'startTime' => '10:55:00', 'endTime' => '11:25:00'],
                    ['name' => 'Period 7', 'position' => 6, 'startTime' => '11:30:00', 'endTime' => '12:00:00'],
                    ['name' => 'Period 8', 'position' => 7, 'startTime' => '12:05:00', 'endTime' => '12:35:00'],
                ]
            ],

            'c' => [
                'name' => 'C',
                'description' => 'Late Start (longer periods)',
                'periods' => [
                    ['name' => 'Period 1', 'position' => 0, 'startTime' => '09:30:00', 'endTime' => '10:10:00'],
                    ['name' => 'Period 2', 'position' => 1, 'startTime' => '10:15:00', 'endTime' => '10:55:00'],
                    ['name' => 'Period 3', 'position' => 2, 'startTime' => '11:00:00', 'endTime' => '11:40:00'],
                    ['name' => 'Period 4', 'position' => 3, 'startTime' => '11:45:00', 'endTime' => '12:25:00'],
                    ['name' => 'Period 5', 'position' => 4, 'startTime' => '12:30:00', 'endTime' => '13:10:00'],
                    ['name' => 'Period 6', 'position' => 5, 'startTime' => '13:15:00', 'endTime' => '13:55:00'],
                    ['name' => 'Period 7', 'position' => 6, 'startTime' => '14:00:00', 'endTime' => '14:30:00'],
                    ['name' => 'Period 8', 'position' => 7, 'startTime' => '14:35:00', 'endTime' => '15:00:00'],
                ]
            ],

            'e' => [
                'name' => 'E',
                'description' => 'Early Start (with Homeroom)',
                'periods' => [
                    ['name' => 'Homeroom', 'position' => 0, 'startTime' => '07:45:00', 'endTime' => '08:00:00'],
                    ['name' => 'Period 1', 'position' => 1, 'startTime' => '08:05:00', 'endTime' => '08:45:00'],
                    ['name' => 'Period 2', 'position' => 2, 'startTime' => '08:50:00', 'endTime' => '09:30:00'],
                    ['name' => 'Period 3', 'position' => 3, 'startTime' => '09:35:00', 'endTime' => '10:15:00'],
                    ['name' => 'Period 4', 'position' => 4, 'startTime' => '10:20:00', 'endTime' => '11:00:00'],
                    ['name' => 'Period 5', 'position' => 5, 'startTime' => '11:05:00', 'endTime' => '11:45:00'],
                    ['name' => 'Period 6', 'position' => 6, 'startTime' => '11:50:00', 'endTime' => '12:30:00'],
                    ['name' => 'Period 7', 'position' => 7, 'startTime' => '12:35:00', 'endTime' => '13:15:00'],
                    ['name' => 'Period 8', 'position' => 8, 'startTime' => '13:20:00', 'endTime' => '13:45:00'],
                ]
            ]
        ];

        return $this->formatter->format($raw);
    }
}

