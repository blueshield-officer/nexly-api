<?php

namespace App\ValueResolver;

use App\Entity\SchoolPeriod;
use App\Repository\SchoolPeriodRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

final readonly class SchoolPeriodValueResolver implements ValueResolverInterface
{
    public function __construct(
        private SchoolPeriodRepository $schoolPeriodRepository,
    ){}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== SchoolPeriod::class) {
            return [];
        }

        $schoolUuid = $request->attributes->get('schoolUuid');
        $periodUuid = $request->attributes->get('periodUuid');

        if ($schoolUuid === null || $periodUuid === null) {
            return [];
        }

        $period = $this->schoolPeriodRepository->findOneBySchoolUuidAndPeriodUuid(
            Uuid::fromString($schoolUuid),
            Uuid::fromString($periodUuid)
        );

        if ($period === null) {
            throw new NotFoundHttpException('Period not found for this school.');
        }

        return [$period];
    }
}
