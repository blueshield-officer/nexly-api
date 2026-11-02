<?php

namespace App\Controller\Api\Sis;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SisController extends AbstractController
{
    #[Route('/sis', name: 'app_sis')]
    public function index(): JsonResponse
    {

    }
}
