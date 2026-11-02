<?php

namespace App\Dto;

use DateTimeImmutable;

readonly class SchoolCalendarDTO
{
    public function __construct(
        public DateTimeImmutable $date,
        public ?string $scheduleId,
        public SchoolCalendarDayOffDTO | null $dayOff,
        public bool $autoChange,
    )
    {}

   public static function fromArray(array $data): self
   {
       return new self(
           date: $data['date'],
           scheduleId: $data['scheduleId'],
           dayOff: is_null($data['dayOff']) ? null : SchoolCalendarDayOffDTO::fromArray($data['dayOff']),
           autoChange: $data['autoChange'],
       );
   }
}
