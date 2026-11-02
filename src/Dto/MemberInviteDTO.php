<?php

namespace App\Dto;

use App\Entity\DistrictMemberInvite;
use App\Entity\SchoolMemberInvite;
use DateTimeImmutable;

readonly class MemberInviteDTO
{
    public function __construct(
        public string $id,
        public string $type,
        public string $uuid,
        public string $name,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $expiresAt,
        public ?DateTimeImmutable $acceptedAt,
    ){}

    public static function fromSchool(SchoolMemberInvite $entity): self
    {
        $school = $entity->getSchool();
        return new self(
            id: $entity->getUuid(),
            type: 'school',
            uuid: $school->getUuid(),
            name: $school->getName(),
            createdAt: $entity->getCreatedAt(),
            expiresAt: $entity->getExpiresAt(),
            acceptedAt: $entity->getAcceptedAt()
        );
    }

    public static function fromDistrict(DistrictMemberInvite $entity): self
    {
        $district = $entity->getDistrict();
        return new self(
            id: $entity->getUuid(),
            type: 'district',
            uuid: $district->getUuid(),
            name: $district->getName(),
            createdAt: $entity->getCreatedAt(),
            expiresAt: $entity->getExpiresAt(),
            acceptedAt: $entity->getAcceptedAt()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'expiresAt' => $this->expiresAt->format('Y-m-d H:i:s'),
            'acceptedAt' => $this->acceptedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
