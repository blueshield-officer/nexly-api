<?php

namespace App\Service\Cache;

use App\Dto\NotificationDto;
use App\Entity\Notification;
use App\Formatter\SchoolFormatter;
use App\Repository\NotificationRepository;
use App\Repository\SchoolCalendarEventRepository;
use App\Repository\SchoolConfigRepository;
use App\Repository\SchoolRepository;
use App\Service\RedisService;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class SchoolCache
{
    private mixed $schoolId;

    public function __construct(
        private readonly RedisService                       $redis,
        private readonly SchoolRepository                   $schoolRepository,
        private readonly SchoolFormatter                    $schoolFormatter,
        private readonly SchoolConfigRepository             $schoolConfigRepository,
        private readonly SchoolCalendarEventRepository      $schoolCalendarEventRepository,
        private readonly NotificationRepository             $notificationRepository,
                                                            $schoolId
    )
    {
        $this->schoolId = $schoolId;
    }

    private function makeKey(array $keys): array
    {
        return array_merge(['school', $this->schoolId], $keys);
    }

    public function clearDay(
        DateTimeInterface $date
    ): void
    {
        $this->redis->delete($this->makeKey(["calendar", $date->format('Y-m-d')]));
    }

    public function getMain()
    {
        return $this->redis->getOrSet(
            $this->makeKey(["main"]),
            function () {
                $data = $this->schoolRepository->findById($this->schoolId);
                if (!is_null($data)) return $this->schoolFormatter->format($data);
                return null;
            }
        );
    }

    public function getConfig()
    {
        return $this->redis->getOrSet(
            $this->makeKey(["config"]),
            function () {
                return $this->schoolConfigRepository->findOneBySchoolId($this->schoolId);
            }
        );
    }

    public function getNotifications(
        int | null $districtId
    ) : array
    {
        return $this->redis->getOrSet(
            $this->makeKey(["notifications"]),
            function () use ($districtId) {
                return array_map(
                    fn (Notification $n) => NotificationDto::fromEntity($n),
                    $this->notificationRepository->findFeedBySchoolIdAndDistrictId($this->schoolId, $districtId),
                );
            });
    }

    public function getCalendarStructure()
    {
        return $this->redis->getOrSet(
            $this->makeKey(["calendar", "structure"]),
            function () {
                return $this->schoolCalendarEventRepository->findStructureBySchoolId(
                    $this->schoolId,
                );
            },
        );
    }

    private function secondsUntilMidnight(DateTimeImmutable $today): int
    {
        $tll = $today->setTime(0, 0)->modify('+1 day')->getTimestamp() - $today->getTimestamp();
        return max(1, $tll);
    }
}
