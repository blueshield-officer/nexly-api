<?php

namespace App\Sis\Storage\Calendar;

use App\Repository\SchoolCalendarEventRepository;
use App\Service\RedisService;


final readonly class SchoolCalendarEventStorage
{
    public function __construct(
        private SchoolCalendarEventRepository $schoolCalendarEventRepository,
        private RedisService             $redis,
    )
    {
    }

    public function hasSchoolYearConfigured(int $schoolId): bool {
        return $this->redis->getOrSet(
            $this->makeKey($schoolId, ['year_configured']), function () use ($schoolId) {
            return $this->schoolCalendarEventRepository->hasSchoolYearConfigured($schoolId);
        });
    }

    public function isWithinSchoolYear(int $schoolId, \DateTimeImmutable $date): bool {
        return $this->redis->getOrSet(
            $this->makeKey($schoolId, ['within_year', $date->format('Y-m-d')]), function () use ($schoolId, $date) {
                return $this->schoolCalendarEventRepository->isWithinSchoolYear($schoolId, $date);
        });
    }

    public function getSchoolYearStartDate(int $schoolId): ?\DateTimeImmutable
    {
        return $this->redis->getOrSet(
            $this->makeKey($schoolId, ['year_start']), function () use ($schoolId) {
                return $this->schoolCalendarEventRepository->getSchoolYearStartDate($schoolId, new \DateTimeImmutable());
        });

    }


    public function invalidate(int $schoolId, array $array): void
    {
        $this->redis->delete(
            $this->makeKey($schoolId, $array)
        );
    }
    private function makeKey(int $schoolId, array $array): array
    {
        return array_merge(['school', $schoolId, 'calendar', 'events'], $array);
    }
}
