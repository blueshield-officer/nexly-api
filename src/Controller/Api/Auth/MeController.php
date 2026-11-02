<?php

namespace App\Controller\Api\Auth;

use App\Entity\User;
use App\Enum\UserRoleEnum;
use App\Repository\DistrictMembershipRepository;
use App\Repository\SchoolMembershipRepository;
use App\Resolver\UserRoleResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MeController extends AbstractController
{
    #[Route('/me', name: 'app_api_auth_me')]
    public function index(
        #[CurrentUser] ?User $user,
        DistrictMembershipRepository $districtMembershipRepository,
        SchoolMembershipRepository $schoolMembershipRepository,
        UserRoleResolver $userRoleResolver
    ): JsonResponse
    {
        $role = $userRoleResolver->resolve($user);

        $uuid = match ($role) {
            UserRoleEnum::DistrictAdmin, UserRoleEnum::DistrictStaff => $districtMembershipRepository->findDistrictUuidByUser($user),
            UserRoleEnum::SchoolAdmin, UserRoleEnum::SchoolStaff => $schoolMembershipRepository->findSchoolUuidByUser($user),
            default => null,
        };

        return $this->json([
            'uuid' => $uuid,

            'role' => $role,
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName() ?? null,
            'lastName'  => $user->getLastName() ?? null,
        ]);
    }
}
