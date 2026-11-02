<?php

namespace App\Dto;

use App\Enum\ServerModuleEnum;
use App\Enum\ServerStatusEnum;
use App\Enum\ServerStatusScopeEnum;

readonly class ServerStatusDTO
{
    public function __construct(
        public ServerStatusScopeEnum $scope,
        public ServerModuleEnum      $service,
        public ServerStatusEnum      $status,
        public ?string               $message,
        public bool                  $isActive,
        public \DateTimeImmutable    $createdAt,
        public \DateTimeImmutable    $updatedAt,
        public ?int                  $createdBy = null,
    ){}

    static function fromArray(array $data): self
    {
        return new self(
            scope: $data['scope'],
            service: $data['service'],
            status: $data['status'],
            message: $data['message'],
            isActive: $data['isActive'],
            createdAt: $data['createdAt'],
            updatedAt: $data['updatedAt'],
            createdBy: $data['createdBy']
        );
    }
}
