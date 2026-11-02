<?php

namespace App\Serializer\Normalizer;

use App\Entity\SchoolCalendar;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SchoolCalendarNormalizer
{
    public function normalize(SchoolCalendar $calendar): array
    {
        $dayOff = $calendar->getDayOff();

        return [
            'date'         => $calendar->getDate()->format('Y-m-d'),
            'scheduleUuid' => $calendar->getSchedule()?->getUuid()?->toRfc4122(),
            'dayCycleUuid' => $calendar->getDayCycle()?->getUuid()?->toRfc4122(),
            'status'       => $calendar->getStatus()->value,
            'dayOff'       => $dayOff !== null ? [
                'title'       => $dayOff->getTitle(),
                'description' => $dayOff->getDescription(),
                'scope'       => $dayOff->getScope()?->isEmpty() !== false
                    ? null
                    : array_map(fn($g) => (string) $g->getUuid(), $dayOff->getScope()->toArray()),
            ] : null,
            'hasRecord' => true
        ];
    }
}
