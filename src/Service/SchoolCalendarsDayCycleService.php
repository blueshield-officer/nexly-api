<?php

namespace App\Service;

use App\Entity\SchoolDayCycle;
use App\Repository\SchoolCalendarRepository;
use App\Sis\Storage\Calendar\SchoolCalendarEventStorage;
use App\Sis\Storage\DayCycle\SchoolDayCycleStorage;

final readonly class SchoolCalendarsDayCycleService
{
    public function __construct(
        private SchoolCalendarRepository $calendarRepository,
        private SchoolDayCycleStorage $dayCycleStorage,
        private SchoolCalendarEventStorage $calendarEventStorage,
        private SchoolInstructionalDayChecker $checker,
    ){}

    public function resolve(int $schoolId, \DateTimeImmutable $date): string | null
    {
        $days = $this->dayCycleStorage->get($schoolId);
        if ($days === []) return null;

        $indexedDays = [];
        foreach ($days as $uuid => $day) {
            $indexedDays[$day['position']] = $uuid;
        }


        $date = $date->setTime(0, 0);
        [$anchorDate, $anchorIndex] = $this->findAnchor($schoolId, $date, $indexedDays);

        $records = $this->calendarRepository->findAllEntitiesByDateRange($schoolId, $anchorDate, $date);

        $count = 0;
        for ($cursorDate = $anchorDate->modify('+1 day'); $cursorDate <= $date; $cursorDate = $cursorDate->modify('+1 day')) {
            $record = $records[$cursorDate->format('Y-m-d')] ?? null;
            if ($record !== null) {
                // TODO unify this with a status
                if (!$this->checker->isInstructional($schoolId, $cursorDate, $records[$cursorDate->format('Y-m-d')])) continue;
            }
            else {
                if ((int)$cursorDate->format('N') >= 6) continue;
            }
            $count++;
        }

        $index = $anchorIndex + $count;
        if ($index < 0) {
            return null;
        }

        return $indexedDays[$index % count($indexedDays)];
    }

    /**
     * @param list<SchoolDayCycle> $days ordered by position
     * @return array{0: \DateTimeImmutable, 1: int}
     */
    private function findAnchor(int $schoolId, \DateTimeImmutable $date, array $days): array
    {
        $record = $this->calendarRepository->findLatestWithDayCycleOnOrBefore($schoolId, $date);

        $cycle = $record?->getDayCycle();

        if ($record !== null && $cycle !== null && $cycle->getUuid() !== null) {
            foreach ($days as $position => $dayCycleUuid) {
                if ($dayCycleUuid === (string)$cycle->getUuid()) {
                    return [$record->getDate(), $position];
                }
            }
        }

        $yearStart = $this->calendarEventStorage->getSchoolYearStartDate($schoolId);

        return [$yearStart->modify('-1 day'), -1];
    }
}
