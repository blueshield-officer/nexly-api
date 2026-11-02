<?php

namespace App\Controller\Api\District;

use App\Dto\DistrictDTO;
use App\Factory\DistrictCacheFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class DistrictController extends AbstractController
{
    #[Route(['/{districtUuid}', '/'], name: 'app_api_district_search')]
    public function search(
        DistrictDTO $districtDto,
    ): JsonResponse
    {
        return new JsonResponse($districtDto->jsonSerialize());
    }

    #[Route(['/{districtUuid}/schools'], name: 'app_api_district_schools')]
    public function schools(
        DistrictDTO $districtDto,
        DistrictCacheFactory $districtCacheFactory,
    ): JsonResponse
    {
        $districtCache = $districtCacheFactory->create($districtDto->id);
        return new JsonResponse($districtCache->getSchools());
    }
}
