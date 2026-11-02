<?php

namespace App\Sis\Dto\Schedule;

class SisSchedulePeriodDto
{
    public function __construct(
        public string      $name,
        public int         $position,
        public string      $startTime,
        public string|null $endTime,
    ){}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            position: $data['position'],
            startTime: $data['start_time'],
            endTime: $data['end_time'],
        );
    }
}
