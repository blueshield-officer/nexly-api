<?php

namespace App\Dto\Request\Util;

use ReflectionProperty;

final class PatchFieldChecker
{
    public static function isProvided(object $dto, string $property): bool
    {
        $reflectionProperty = new ReflectionProperty($dto, $property);
        return $reflectionProperty->isInitialized($dto);
    }
}
