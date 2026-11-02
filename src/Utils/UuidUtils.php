<?php

namespace App\Utils;

use Symfony\Component\Uid\Uuid;

final class UuidUtils
{
    /**
     * @param string[] $uuidStrings
     * @return Uuid[]
     */
    public static function fromStrings(array $uuidStrings): array
    {
        return array_map(Uuid::fromString(...), $uuidStrings);
    }
}
