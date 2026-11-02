<?php

namespace App\Controller\Api\Request;

use App\Dto\SchoolRequestDTO;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SchoolRequestController extends BaseRequestController
{
    #[Route('/school', name: 'app_school_request', methods: 'POST')]
    public function schoolRequest(#[CurrentUser] User $user, Request $request): JsonResponse
    {
//        $dto = SchoolRequestDTO::fromArray($request->toArray());
//        if ($error = $this->validateAndCheck($user, $dto)) return $error;
//
//        $this->requestService->createSchool($user, $dto);
//        return $this->json(['message' => 'School request submitted!']);

        return new JsonResponse([]);
    }
}
