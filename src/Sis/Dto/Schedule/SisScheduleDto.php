<?php

namespace App\Sis\Dto\Schedule;

class SisScheduleDto
{
    public function __construct(
        public string                         $externalId,
        public array                          $periods,
    ) {}

    public static function fromArray(
        string $externalId,
        array $periods,
    ): self {
        return new self(
            externalId: $externalId,
            periods: array_map(
                fn($period) => SisSchedulePeriodDto::fromArray($period),
                $periods
            )
        );
    }
}
