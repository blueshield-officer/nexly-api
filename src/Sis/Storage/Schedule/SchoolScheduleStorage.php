<?php

namespace App\Sis\Storage\Schedule;

use App\Entity\School;
use App\Entity\SchoolSchedule;
use App\Entity\SchoolSchedulePeriod;
use App\Repository\SchoolScheduleRepository;
use App\Service\RedisService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class SchoolScheduleStorage
{
    public function __construct(
        private SchoolScheduleRepository $repository,
        private EntityManagerInterface   $em,
        private RedisService             $redis,
    ) {}

    public function get(int $schoolId) : array
    {
        $result = $this->redis->getOrSet(['school', $schoolId, "schedules"], function () use ($schoolId) {
            $data =  $this->repository->findAllBySchoolId($schoolId);
            if ($data !== []) return $data;
            return null;
        });

        return $result ?? [];
    }

    public function invalidate(int $schoolId): void
    {
        $this->redis->delete(['school', $schoolId, "schedules"]);
    }




    //////////////////////////////////////////////////////////////////
    ///
    ///         SIS HELL
    ///
    /////////////////////////////////////////////////////////////////////
    public function upsert(int $schoolId, array $formatted): void
    {
        $school = $this->em->getReference(School::class, $schoolId);

        $stored = $this->repository->findAllBySchoolId($schoolId);

        $hasChanges = false;

        foreach ($formatted as $externalId => $schedule) {
            if (isset($stored[$externalId])) {
                if (!$this->hasChanged($stored[$externalId], $schedule)) continue;
            }
            $hasChanges = true;
            $this->record($school, $externalId, $schedule);
        }

        if ($hasChanges) $this->em->flush();
    }
    private function record(School $school, string $externalId, array $schedule): void
    {
        $scheduleEntity = new SchoolSChedule();
        $scheduleEntity->setSchool($school);
        $scheduleEntity->setExternalId($externalId);

        $this->em->persist($scheduleEntity);

        foreach ($schedule['periods'] as $period) {
            $periodEntity = new SchoolSchedulePeriod();
            $periodEntity->setSchedule($scheduleEntity);
            $periodEntity->setName($period['name']);
            $periodEntity->setStartTime(new DateTimeImmutable($period['startTime']));
            $periodEntity->setEndTime(is_null($period['endTime']) ? null : new DateTimeImmutable($period['endTime']));
            $periodEntity->setPosition($period['position']);
            $this->em->persist($periodEntity);
        }
    }

    private function hasChanged(array $oldSchedule, array $newSchedule): bool
    {
        if ($oldSchedule['periods'] !== $newSchedule['periods']) return true;
        return false;
    }
}
