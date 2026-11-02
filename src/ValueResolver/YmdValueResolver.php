<?php

namespace App\ValueResolver;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class YmdValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== DateTimeImmutable::class) {
            return [];
        }

        $ymd = $request->attributes->get('ymd');
        if ($ymd === null) {
            return [];
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $ymd);
        if ($date === false) {
            throw new BadRequestHttpException('Invalid ymd format, expected Y-m-d.');
        }

        return [$date];
    }
}
