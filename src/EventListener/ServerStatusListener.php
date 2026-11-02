<?php

namespace App\EventListener;

use App\Dto\ServerStatusDTO;
use App\Entity\ServerStatus;
use App\Enum\ServerStatusEnum;
use App\Repository\ServerStatusRepository;
use App\Service\RedisService;
use Redis;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class ServerStatusListener
{
    public function __construct(
        private RedisService           $redisService,
        private ServerStatusRepository $serverStatusRepository,
    ){}
    #[AsEventListener(event: KernelEvents::REQUEST, priority: 10)]
    public function onRequestEvent(
        RequestEvent $event,
    ): void
    {
        $status = $this->redisService->getOrSet(['server', 'status'], function () use ($event) {
            $status = $this->serverStatusRepository->getActiveServerStatuses();

            if (is_null($status)) return null;

            return ServerStatusDTO::fromArray([
                'scope' => $status->getScope(),
                'service' => $status->getService(),
                'status' => $status->getStatus(),
                'isActive' => $status->isActive(),
                'message' => $status->getMessage(),
                'createdAt' => $status->getCreatedAt(),
                'updatedAt' => $status->getUpdatedAt(),
                'createdBy' => $status->getCreatedBy()?->getId(),
            ]);
        }, 60);

        if (is_null($status)) return;

        if ($status->status === ServerStatusEnum::Ok) return;

        $event->setResponse(new JsonResponse(
            [
                'success'    => false,
                'status'     => $status->status,
                'message'    => $status->message,
//                'retryAfter' => $status->retryAfter,
            ],
            Response::HTTP_SERVICE_UNAVAILABLE // 503
        ));
    }
}
