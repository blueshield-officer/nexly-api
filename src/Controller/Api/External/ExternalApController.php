<?php

namespace App\Controller\Api\External;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ExternalApController extends AbstractController
{
    #[Route('/ap/subjects', name: 'app_external_ap_subjects')]
    public function subjects(): JsonResponse
    {
        return new JsonResponse([]);
    }

    #[Route('/ap/exams', name: 'app_external_ap_exams')]
    public function exams(): JsonResponse
    {
        return new JsonResponse([]);
    }
}
