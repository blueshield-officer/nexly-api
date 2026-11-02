<?php

namespace App\ValueResolver;

use App\Dto\DistrictDTO;
use App\Exception\NotFound\DistrictNotFoundException;
use App\Factory\DistrictCacheFactory;
use App\Service\Cache\GlobalCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

final readonly class DistrictDtoResolver implements ValueResolverInterface
{
    public function __construct(
        private GlobalCache          $globalCache,
        private DistrictCacheFactory $districtCacheFactory,
    )
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== DistrictDTO::class) {
            return [];
        }

        $uuid = $request->attributes->get('districtUuid');
        if (!Uuid::isValid($uuid)) {
            throw new DistrictNotFoundException();
        }

        $id = $this->globalCache->getDistrictId(Uuid::fromString($uuid));

        if (is_null($id)) throw new DistrictNotFoundException();

        $data = $this->districtCacheFactory->create($id)->getMain();

        yield DistrictDTO::fromArray($data);
    }
}
