<?php

namespace App\Serializer\Normalizer;

use App\Entity\District;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class DistrictNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $context['district_normalizer_called'] = true;

        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = [
            'id',
            'uuid',
            'schools'
        ];

        $data = $this->normalizer->normalize($object, $format, $context);
        $data["uuid"] = $object->getUuid()->toRfc4122();

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof District;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [District::class => true];
    }
}
