<?php

namespace App\Service\Cache;

use App\Enum\SchoolIdentifierEnum;
use App\Repository\DistrictRepository;
use App\Repository\SchoolRepository;
use App\Service\RedisService;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\CacheInterface;

readonly class GlobalCache
{
    public function __construct(
        private RedisService $redis,
        private SchoolRepository $schoolRepository,
        private DistrictRepository $districtRepository,
    ){}

    private function makeKey(array $keys) : array
    {
        array_unshift($keys, 'global');
        return $keys;
    }

    public function getSchoolId(
        Uuid | string    $identifier,
    ) : int | null
    {
        $identifierType = Uuid::isValid($identifier) ? SchoolIdentifierEnum::UUID : SchoolIdentifierEnum::CODE;

        $key = $this->makeKey([$identifierType->value, $identifier]);
        if ($this->redis->exists($key)) return $this->redis->get($key);

        $id = $this->schoolRepository->findIdPublicly($identifier, $identifierType);

        $this->redis->set($key, $id);

        return $id;
    }

    public function getDistrictId(Uuid $uuid) : int | null
    {
        $key = $this->makeKey(['uuid', $uuid]);
        if ($this->redis->exists($key)) return $this->redis->get($key);

        $id = $this->districtRepository->findIdPublicly($uuid);

        $this->redis->set($key, $id);

        return $id;
    }
}
