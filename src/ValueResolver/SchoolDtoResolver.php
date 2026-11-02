<?php

namespace App\ValueResolver;

use App\Dto\DistrictDTO;
use App\Dto\SchoolDto;
use App\Exception\NotFound\SchoolNotFoundException;
use App\Factory\DistrictCacheFactory;
use App\Factory\SchoolCacheFactory;
use App\Resolver\IdentifierResolver;
use App\Service\Cache\GlobalCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class SchoolDtoResolver implements ValueResolverInterface
{
    public function __construct(
        private GlobalCache $globalCache,
        private SchoolCacheFactory $schoolCacheFactory,
        private DistrictCacheFactory $districtCacheFactory,
        private IdentifierResolver $identifierResolver,
    )
    {}
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== SchoolDto::class) {
            return [];
        }

        $idSource = $this->identifierResolver->resolveFromUuidOrIdentifier(
            $request->attributes->get('schoolUuid'),
            $request->attributes->get('identifier'),
        );

        $id = $this->globalCache->getSchoolId($idSource);
        if ($id === null) throw new SchoolNotFoundException();

        $data = $this->schoolCacheFactory->create($id)->getMain();

        if ($request->query->has('district') && $data['districtId']) {
            $district = $this->districtCacheFactory
                ->create($data['districtId'])
                ->getMain();

            $data['district'] = DistrictDto::fromArray($district)->jsonSerialize();
        }

        yield SchoolDto::fromArray($data);
    }
}
