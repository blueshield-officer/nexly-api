<?php

namespace App\Service;

use App\Entity\School;
use App\Entity\SchoolCalendar;
use App\Entity\SchoolSchedule;
use App\Enum\SchoolCalendarDayStatusEnum;
use App\Repository\SchoolCalendarRepository;
use App\Serializer\Normalizer\SchoolCalendarNormalizer;
use App\Sis\Storage\Calendar\SchoolCalendarEventStorage;

final readonly class SchoolCalendarService
{
    public function __construct(
        private SchoolCalendarRepository       $schoolCalendarRepository,
        private SchoolCalendarsDayCycleService $dayCycleService,
        private SchoolCalendarEventStorage     $schoolCalendarEventStorage,
        private SchoolCalendarNormalizer       $normalizer,
    )
    {
    }

    public function getStatusForDate(int $schoolId, \DateTimeImmutable $date): array
    {
        $calendar = $this->schoolCalendarRepository->findOneBySchoolIdAndDate($schoolId, $date);

        if ($calendar !== null) {
            $data =  $this->normalizer->normalize($calendar);

            if (
                $calendar->getDayCycle() === null
                && $calendar->isDayCycleAutofill()
                && $calendar->getStatus() !== SchoolCalendarDayStatusEnum::DAY_OFF
            ) {
                $data['dayCycleUuid'] = $this->dayCycleService->resolve($schoolId, $date);
            }

            return $data;
        }

        if (!$this->schoolCalendarEventStorage->hasSchoolYearConfigured($schoolId)) {
            return $this->buildStatus($date, SchoolCalendarDayStatusEnum::UNCONFIGURED);
        }

        if (!$this->schoolCalendarEventStorage->isWithinSchoolYear($schoolId, $date) || $this->isWeekend($date)) {
            return $this->buildStatus($date, SchoolCalendarDayStatusEnum::NO_SCHOOL);
        }

        return $this->buildStatus($date, SchoolCalendarDayStatusEnum::REGULAR, $this->dayCycleService->resolve($schoolId, $date));
    }

    private function isWeekend(\DateTimeInterface $date): bool
    {
        return (int)$date->format('N') >= 6;
    }


    private function buildStatus(\DateTimeImmutable $date, SchoolCalendarDayStatusEnum $status, ?string $dayCycle = null): array
    {
        return [
            'date' => $date->format('Y-m-d'),
            'status' => $status->value,
            'scheduleUuid' => null,
            'dayCycleUuid' => $dayCycle,
            'dayOff'       => null,
            'hasRecord'    => false,
        ];
    }
}
