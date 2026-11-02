<?php

namespace App\Serializer\Normalizer;

use App\Entity\School;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

readonly class SchoolNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {}

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $context['school_normalizer_called'] = true;

        // Prevent ObjectNormalizer from touching UUID
        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = [
            'id',
            'uuid',
            'district',
            'colors',
            'schedules',
            'emailDomains',
            'grades',
            'sis',
            'calendar',
            'private',
            'schoolSchedules',
        ];

        $data = $this->normalizer->normalize($object, $format, $context);

        $data['uuid'] = $object->getUuid()->toRfc4122();
        $data['districtId'] = $object->getDistrictId();
        $data['colors'] = array_map(fn($color) => [
            'hex' => '#' . $color->getHex(),
            'base' => $color->isBase()
        ], $object->getColors()->toArray());
        $data['grades'] = [];
        foreach ($object->getGrades()->toArray() as $grade) {
            $data['grades'][$grade->getGrade()] = [
                'name' => $grade->getGradeName(),
                'color' => is_null($grade->getHex()) ? null : '#' . $grade->getHex(),
            ];
        }
        $data['sis'] = $object->getSis()->getName();

        return $data;

    }


    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof School;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [School::class => true];
    }
}
