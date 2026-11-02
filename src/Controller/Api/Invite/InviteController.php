<?php

namespace App\Controller\Api\Invite;

use App\Repository\DistrictMemberInviteRepository;
use App\Repository\SchoolMemberInviteRepository;
use App\Service\MemberInviteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class InviteController extends AbstractController
{
    #[Route('/', name: 'app_invites')]
    public function index(
        MemberInviteService $memberInviteService,
    ): JsonResponse
    {
        return new JsonResponse([
            'invites' => $memberInviteService->getActiveInvites()
        ]);
    }

}
