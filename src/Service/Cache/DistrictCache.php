<?php

namespace App\Service\Cache;

use App\Factory\SchoolCacheFactory;
use App\Repository\DistrictRepository;
use App\Repository\SchoolRepository;
use App\Serializer\Normalizer\DistrictNormalizer;
use App\Service\RedisService;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DistrictCache
{
    private int $districtId;

    public function __construct(
        private readonly RedisService  $redis,
        private readonly DistrictRepository $districtRepository,
        private readonly SchoolRepository $schoolRepository,
        private readonly SchoolCacheFactory $schoolCacheFactory,
                                        $districtId
    ){
        $this->districtId = $districtId;
    }

    private function makeKey(array $keys): array
    {
        return array_merge(['district', $this->districtId], $keys);
    }

    public function getMain()
    {
        return $this->redis->getOrSet(
            $this->makeKey(["main"]),
            function () {
                return $this->districtRepository->findById($this->districtId);
            }
        );
    }

    private function getSchoolsIds(){
        return $this->redis->getOrSet(
            $this->makeKey(['schools']),
            function () {
                return $this->schoolRepository->findSchoolsIdByDistrictId($this->districtId);
            }
        );
    }

    public function getSchools(): array
    {
        $schoolIds = $this->getSchoolsIds();

        return array_map(
            fn(int $id) => $this->schoolCacheFactory->create($id)->getMain(),
            $schoolIds
        );
    }
}
