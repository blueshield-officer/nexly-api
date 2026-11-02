<?php

namespace App\Factory;

use App\Formatter\SchoolFormatter;
use App\Repository\NotificationRepository;
use App\Repository\SchoolCalendarEventRepository;
use App\Repository\SchoolConfigRepository;
use App\Repository\SchoolRepository;
use App\Repository\SchoolCalendarRepository;
use App\Repository\SchoolScheduleRepository;
use App\Serializer\Normalizer\SchoolCalendarNormalizer;
use App\Serializer\Normalizer\SchoolConfigNormalizer;
use App\Serializer\Normalizer\SchoolNormalizer;
use App\Serializer\Normalizer\SchoolScheduleNormalizer;
use App\Service\Cache\GlobalCache;
use App\Service\Cache\SchoolCache;
use App\Service\RedisService;
use Symfony\Contracts\Cache\CacheInterface;

readonly class SchoolCacheFactory
{
    public function __construct(
        private RedisService                  $redis,
        private SchoolRepository              $schoolRepository,
        private SchoolFormatter               $schoolFormatter,
        private SchoolConfigRepository        $schoolConfigRepository,
        private SchoolCalendarEventRepository $schoolCalendarEventRepository,
        private NotificationRepository        $notificationRepository,
    )
    {
    }

    public function create(
        int $schoolId
    ): SchoolCache
    {
        return new SchoolCache(
            $this->redis,
            $this->schoolRepository,
            $this->schoolFormatter,
            $this->schoolConfigRepository,
            $this->schoolCalendarEventRepository,
            $this->notificationRepository,
            $schoolId
        );
    }
}
