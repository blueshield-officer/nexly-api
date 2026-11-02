<?php

namespace App\Dto;

use App\Entity\SchoolRequest;

readonly class SchoolRequestDTO extends BaseRequestDTO
{

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
        );
    }

    public static function fromEntity(SchoolRequest $entity): self
    {
        return new self(
            firstName: $entity->contact->firstName,
            lastName: $entity->contact->lastName,
            role: $entity->contact->role,
            name: $entity->getSchoolName(),
            city: $entity->location->city,
            state: $entity->location->state,
            zip: $entity->location->zip,
            street: $entity->location->street,
            submittedAt: $entity->getSubmittedAt(),
            status: $entity->getStatus(),
        );
    }
}
