<?php

namespace App\Dto;

use DateTimeImmutable;

readonly class SchoolSchedulePeriodDTO
{
    public function __construct(
        public int $schoolId,
        public int $position,
        public string $scheduleId,
        public string $name,
        public DateTimeImmutable $startTime,
        public DateTimeImmutable $endTime
    )
    {}

    static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            position: $data['position'],
            scheduleId: $data['schedule_id'],
            name: $data['name'],
            startTime: $data['start_time'],
            endTime: $data['end_time'] ?? null
        );
    }
}
