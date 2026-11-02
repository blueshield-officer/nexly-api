<?php

namespace App\Controller\Api\Request;

use App\Dto\BaseRequestDTO;
use App\Dto\DistrictRequestDTO;
use App\Dto\SchoolRequestDTO;
use App\Entity\User;
use App\Repository\DistrictRequestRepository;
use App\Repository\SchoolRequestRepository;
use App\Service\RequestService;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

abstract class BaseRequestController extends AbstractController
{
    public function __construct(
        protected readonly RequestService $requestService,
        protected readonly DistrictRequestRepository $districtRequestRepository,
        protected readonly SchoolRequestRepository $schoolRequestRepository,
    ) {}
    #[Route('/check', name: 'app_request_check')]
    public function checkRequest(
        #[CurrentUser] User $user,
    ): JsonResponse
    {
        $dto = $this->requestService->findActive($user);

        if (!$dto) return $this->json(['message' => 'Request not found.'], 404);

        return $this->json($dto->toArray());
    }

    #[Route('/', name: 'app_request_revoke', methods: ['DELETE'])]
    public function revoke(
        #[CurrentUser] User $user,
    ): JsonResponse {
        $this->requestService->revoke($user);
        return $this->json(null, 204);
    }

    #[Route('/history', name: 'app_request_history')]
    public function requestHistory(#[CurrentUser] User $user): JsonResponse
    {
        $data = [
            ...$this->districtRequestRepository->findInactive($user),
            ...$this->schoolRequestRepository->findInactive($user),
        ];

        return $this->json($data);
    }

    protected function getClass(string $type): DistrictRequestRepository | SchoolRequestRepository
    {
        return match ($type) {
            'district' => $this->schoolRequestRepository,
            'school' => $this->districtRequestRepository,
        };
    }

    protected function validateAndCheck(User $user, BaseRequestDTO $dto): ?JsonResponse
    {
        $missing = $dto->validate();
        if (!empty($missing)) return $this->json(['error' => 'Missing fields', 'fields' => $missing], 422);

        if ($this->requestService->hasActiveRequest($user)) {
            return $this->json(['message' => 'You already have an active request.'], 409);
        }

        return null;
    }

    protected function notFound(string $type): JsonResponse
    {
        return $this->json(['error' => "No active {$type} request found."], 404);
    }
}
