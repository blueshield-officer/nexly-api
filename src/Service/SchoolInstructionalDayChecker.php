<?php

namespace App\Service;

use App\Entity\SchoolCalendar;
use App\Sis\Storage\Calendar\SchoolCalendarEventStorage;

final readonly class SchoolInstructionalDayChecker
{
    public function __construct(
        private SchoolCalendarEventStorage $eventStorage,
    ) {}

    public function isInstructional(int $schoolId, \DateTimeImmutable $date, SchoolCalendar $record): bool
    {
        if (!$record->isDayCycleAutofill()) return false;


        $dayOff = $record->getDayOff();
        if ($dayOff !== null && $dayOff->isFullDayOff()) return false;

        return $this->eventStorage->hasSchoolYearConfigured($schoolId)
            && $this->eventStorage->isWithinSchoolYear($schoolId, $date)
            && (int) $date->format('N') < 6;
    }
}
