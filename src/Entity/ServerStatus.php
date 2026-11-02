<?php

namespace App\Entity;

use App\Enum\ServerModuleEnum;
use App\Enum\ServerStatusEnum;
use App\Enum\ServerStatusScopeEnum;
use App\Repository\ServerStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServerStatusRepository::class)]
class ServerStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', enumType: ServerStatusScopeEnum::class)]
    private ServerStatusScopeEnum $scope = ServerStatusScopeEnum::Global;

    #[ORM\Column(type: 'string', enumType: ServerModuleEnum::class)]
    private ServerModuleEnum $service = ServerModuleEnum::All;

    #[ORM\Column(type: 'string', enumType: ServerStatusEnum::class)]
    private ServerStatusEnum $status = ServerStatusEnum::Locked;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $message = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    private ?User $createdBy = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ServerModuleEnum
    {
        return $this->service;
    }

    public function setService(ServerModuleEnum $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getStatus(): ServerStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ServerStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getScope(): ServerStatusScopeEnum
    {
        return $this->scope;
    }

    public function setScope(ServerStatusScopeEnum $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
