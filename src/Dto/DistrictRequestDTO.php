<?php

namespace App\Dto;

use App\Entity\DistrictRequest;

readonly class DistrictRequestDTO extends BaseRequestDTO
{
    public function __construct(
        null|string                    $firstName,
        null|string                    $lastName,
        null|string                    $role,
        null|string                    $name,
        null|string                    $city,
        null|string                    $state,
        null|string                    $zip,
        null|string                    $street,
        public null|int                $estimatedSchools,

        public \DateTimeImmutable|null $submittedAt = null,
    )
    {
        parent::__construct($firstName, $lastName, $role, $name, $city, $state, $zip, $street);
    }

    public static function fromArray(array $data): self
    {
        $base = self::contactAndLocation($data);

        return new self(
            firstName: $base['firstName'],
            lastName: $base['lastName'],
            role: $base['role'],
            name: $base['name'],
            city: $base['city'],
            state: $base['state'],
            zip: $base['zip'],
            street: $base['street'],
            estimatedSchools: isset($data['estimatedSchools']) ? (int)$data['estimatedSchools'] : null,
        );
    }

    public static function fromEntity(DistrictRequest $entity): self
    {
        return new self(
            firstName: $entity->contact->firstName,
            lastName: $entity->contact->lastName,
            role: $entity->contact->role,
            name: $entity->getDistrictName(),
            city: $entity->location->city,
            state: $entity->location->state,
            zip: $entity->location->zip,
            street: $entity->location->street,
            estimatedSchools: $entity->getEstimatedSchools(),
            submittedAt: $entity->getSubmittedAt(),
        );
    }
}
