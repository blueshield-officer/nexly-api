<?php

namespace App\Factory;

use App\Repository\DistrictRepository;
use App\Repository\SchoolRepository;
use App\Serializer\Normalizer\DistrictNormalizer;
use App\Service\Cache\DistrictCache;
use App\Service\Cache\GlobalCache;
use App\Service\RedisService;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\CacheInterface;

readonly class DistrictCacheFactory
{
    public function __construct(
        private RedisService       $redis,
        private DistrictRepository $districtRepository,
        private SchoolRepository   $schoolRepository,
        private SchoolCacheFactory $schoolCacheFactory,

    ){}

    public function create(int $districtId): DistrictCache
    {
        return new DistrictCache(
            $this->redis,
            $this->districtRepository,
            $this->schoolRepository,
            $this->schoolCacheFactory,
            $districtId
        );
    }
}
