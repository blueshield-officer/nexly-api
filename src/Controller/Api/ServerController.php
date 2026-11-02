<?php

namespace App\Controller\Api;

use App\Repository\ServerMaintenanceWindowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/server')]
final class ServerController extends AbstractController
{
    #[Route('/maintenance', name: 'app_server_maintenance')]
    public function indexMaintenance(
        ServerMaintenanceWindowRepository $maintenanceWindowRepository
    ): JsonResponse
    {
        $data = $maintenanceWindowRepository->findActive();
        if (!is_null($data)) return new JsonResponse([
            'reason' => $data->getReason(),
            'startsAt' => $data->getStartsAt()->format('Y-m-d H:i:s'),
            'endsAt' => $data->getEndsAt()->format('Y-m-d H:i:s'),
        ]);

        return new JsonResponse([]);
    }
}
