<?php

namespace App\Sis\Storage\Schedule;

use App\Entity\School;
use App\Entity\SchoolPeriod;
use App\Repository\SchoolPeriodRepository;
use App\Service\RedisService;
use Doctrine\ORM\EntityManagerInterface;

readonly class SchoolPeriodStorage
{
    public function __construct(
        private SchoolPeriodRepository $repository,
        private EntityManagerInterface $em,
        private RedisService           $redis,
    ) {}

    public function get(int $schoolId): array
    {
        $result = $this->redis->getOrSet(['school', $schoolId, 'periods'], function () use ($schoolId) {
            $data = $this->repository->findAllBySchoolId($schoolId);
            return $data !== [] ? $data : null;
        });

        return $result ?? [];
    }

    public function findOrCreate(School $school, string $name): SchoolPeriod
    {
        $existing = $this->repository->findOneBySchoolAndName($school, $name);
        if ($existing !== null) {
            return $existing;
        }

        $period = new SchoolPeriod();
        $period->setSchool($school);
        $period->setName($name);

        $this->em->persist($period);

        return $period;
    }

    public function invalidate(int $schoolId): void
    {
        $this->redis->delete(['school', $schoolId, 'periods']);
    }
}
