<?php

namespace App\Serializer\Normalizer;

use App\Entity\SchoolSchedule;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class SchoolScheduleNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $context['school_schedule_normalizer_called'] = true;

        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = [
            'id',
            'school',
            'schoolSchedulePeriods'
        ];

        $data = $this->normalizer->normalize($object, $format, $context);
        $data['periods'] = [];
        foreach ($object->getSchoolSchedulePeriods()->toArray() as $period) {
            $data['periods'][$period->getPosition()] = [
                'name' => $period->getName(),
                'startTime' => $period->getStartTime()->format('H:i:s'),
                'endTime' => $period->getEndTime()?->format('H:i:s'),
            ];
        }

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof SchoolSchedule;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [SchoolSchedule::class => true];
    }
}
