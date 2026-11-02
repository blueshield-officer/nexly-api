<?php

namespace App\Service\Cache;

use App\Repository\ExternalSatRepository;
use App\Service\RedisService;

readonly class ExternalSatCache
{
    public function __construct(
        private RedisService          $redis,
        private ExternalSatRepository $externalSatRepository,
    )
    {}
    private function makeKey(array $keys) : array
    {
        array_unshift($keys, 'external');
        return $keys;
    }

    public function getAll()
    {
        return $this->redis->getOrSet(
            $this->makeKey(["sat"]),
            function () {
                return $this->externalSatRepository->findALl();
            }
        );
    }
}
