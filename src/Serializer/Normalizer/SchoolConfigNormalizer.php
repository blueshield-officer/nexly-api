<?php

namespace App\Serializer\Normalizer;

use App\Entity\SchoolConfig;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SchoolConfigNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    )
    {
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $context['school_config_normalizer_called'] = true;

        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = [
            'id',
            'school',
            'defaultSchedule'
        ];

        $data = $this->normalizer->normalize($object, $format, $context);
        $data['defaultSchedule'] = $object->getDefaultSchedule()->getCode();

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof SchoolConfig;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [SchoolConfig::class => true];
    }
}
