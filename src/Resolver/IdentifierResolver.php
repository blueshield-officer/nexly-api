<?php

namespace App\Resolver;

use App\Exception\NotFound\SchoolNotFoundException;
use Symfony\Component\Uid\Uuid;

final class IdentifierResolver
{
    public function resolveFromUuidOrIdentifier(?string $uuid, ?string $identifier): string
    {
        if ($uuid === null && $identifier === null) {
            throw new SchoolNotFoundException();
        }

        if ($uuid !== null) {
            if (!Uuid::isValid($uuid)) {
                throw new SchoolNotFoundException();
            }
            return $uuid;
        }

        return $identifier;
    }
}
