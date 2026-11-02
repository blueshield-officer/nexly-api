<?php

namespace App\Sis\Storage\DayCycle;

use App\Repository\SchoolDayCycleRepository;
use App\Service\RedisService;

final readonly class SchoolDayCycleStorage
{
    public function __construct(
        private SchoolDayCycleRepository $repository,
        private RedisService             $redis,
    ) {}

    public function get(int $schoolId): array
    {
        $result = $this->redis->getOrSet(['school', $schoolId, 'day-cycles'], function () use ($schoolId) {
            $data = $this->repository->findAllBySchoolId($schoolId);
            return $data !== [] ? $data : null;
        });

        return $result ?? [];
    }

    public function invalidate(int $schoolId): void
    {
        $this->redis->delete(['school', $schoolId, 'day-cycles']);
    }
}
