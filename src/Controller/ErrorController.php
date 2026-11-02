<?php

namespace App\Controller;

use App\Environment\EnvironmentChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

#[AsController]
readonly final class ErrorController
{
    public function __construct(
        private EnvironmentChecker $environmentChecker,
    ) {}
    public function show(Throwable $exception): JsonResponse
    {
        if ($this->environmentChecker->isDev()){
            throw $exception;
        }

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 500;

        $message = $exception instanceof HttpExceptionInterface
            ? $exception->getMessage()
            : 'Internal Server Error';

        return new JsonResponse([
            'error' => true,
            'message' => $message ?: 'An error occurred',
            'code' => $statusCode,
        ], $statusCode);
    }
}
