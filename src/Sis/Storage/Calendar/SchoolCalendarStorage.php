<?php

namespace App\Sis\Storage\Calendar;

use App\Entity\School;
use App\Entity\SchoolCalendar;
use App\Entity\SchoolCalendarDayOff;
use App\Repository\SchoolCalendarEventRepository;
use App\Repository\SchoolCalendarRepository;
use App\Repository\SchoolGradeRepository;
use App\Repository\SchoolScheduleRepository;
use App\Serializer\Normalizer\SchoolCalendarNormalizer;
use App\Service\RedisService;
use App\Service\SchoolCalendarService;
use App\Sis\Helper\DateHelper;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Redis;


readonly class SchoolCalendarStorage
{
    public function __construct(
        private SchoolCalendarRepository $calendarRepository,
        private SchoolScheduleRepository $scheduleRepository,
        private SchoolCalendarEventRepository $calendarEventRepository,
        private SchoolGradeRepository    $schoolGradeRepository,
        private SchoolCalendarService    $schoolCalendarService,
        private EntityManagerInterface   $em,
        private DateHelper               $dateHelper,
        private RedisService             $redis,
    )
    {
    }

    public function save(int $schoolId, DateTimeImmutable $date, array|null $data): void
    {
        $key = $this->makeKey($schoolId, $date);
        $this->redis->set($key, $data);

        if ($this->dateHelper->inPast($date)) {
            $existing = $this->calendarRepository->findOneBySchoolIdAndDate($schoolId, $date);

            if (!is_null($existing)) return;

            $school = $this->em->getReference(School::class, $schoolId);
            $schedule = is_null($data) ? null : $this->scheduleRepository->findBySchoolAndExternalId($school, $data['scheduleId']);

            $calendarDay = new SchoolCalendar();
            $calendarDay->setSchool($school);
            $calendarDay->setDate($date);
            $calendarDay->setSchedule($schedule);
            $calendarDay->setAutoChange(true);

            if (!is_null($data) && isset($data['dayOff'])) {
                $calendarDayOff = new SchoolCalendarDayOff();

                $scope = $data['dayOff']['scope'];
                if (!is_null($scope)) {
                    foreach ($scope as $grade) {
                        dump($grade);
                        $schoolGrade = $this->schoolGradeRepository->findBySchoolAndExternalId($school, $grade);
                        $calendarDayOff->addScope($schoolGrade);
                    }
                }
                $calendarDay->setDayOff($calendarDayOff);
                $this->em->persist($calendarDayOff);
            }

            $this->em->persist($calendarDay);
            $this->em->flush();
        }
    }

    public function getMany(int $schoolId, DateTimeImmutable $from, DateTimeImmutable $to): array
    {

        $dateRange = $this->dateHelper->getDateRange($from, $to);
        $data = [];

        foreach ($dateRange as $date) {
            $data[$date->format('Y-m-d')] = $this->get($schoolId, $date);
        }

        return $data;
    }

    public function get(int $schoolId, DateTimeImmutable $date)
    {
        $key = $this->makeKey($schoolId, $date);
        if ($this->redis->exists($key)) {
            return $this->redis->get($key);
        } else {
            $data = $this->schoolCalendarService->getStatusForDate($schoolId, $date);
            $this->redis->set($key, $data);
            return $data;
        }
    }

    public function invalidate(int $schoolId, DateTimeImmutable $ymd): void
    {
        $schoolYearEndDate = $this->calendarEventRepository->getSchoolYearEndDate($schoolId, $ymd);

        $keysToDelete = [];
        for ($d = $ymd; $d <= $schoolYearEndDate; $d = $d->modify('+1 day')) {
            $keysToDelete[] = $this->makeKey($schoolId, $d);
        }

        $this->redis->deleteMany($keysToDelete);

    }

    private function makeKey(int $schoolId, DateTimeImmutable $date): array
    {
        return [
            'school',
            $schoolId,
            'calendar',
           $date->format('Y-m-d')
        ];
    }
}
