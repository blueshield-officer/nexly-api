<?php

namespace App\Controller\Api\Request;

use App\Dto\DistrictRequestDTO;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STUDENT')]
class DistrictRequestController extends BaseRequestController
{
    #[Route('/district', name: 'app_district_request', methods: 'POST')]
    public function districtRequest(#[CurrentUser] User $user, Request $request): JsonResponse
    {
//        $dto = DistrictRequestDTO::fromArray($request->toArray());
//        if ($error = $this->validateAndCheck($user, $dto)) return $error;
//
//        $this->requestService->createDistrict($user, $dto);
//        return $this->json(['message' => 'District request submitted!']);

        return new JsonResponse([]);

    }
}
