<?php

namespace App\ValueResolver;

use App\Entity\SchoolDayCycle;
use App\Exception\NotFound\SchoolDayCycleNotFoundException;
use App\Repository\SchoolDayCycleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

final readonly class SchoolDayCycleValueResolver implements ValueResolverInterface
{
    public function __construct(
        private SchoolDayCycleRepository $schoolDayCycleRepository,
    ){}
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== SchoolDayCycle::class) {
            return [];
        }

        $schoolUuid = $request->attributes->get('schoolUuid');
        $dayCycleUuid = $request->attributes->get('dayCycleUuid');

        if ($schoolUuid === null || $dayCycleUuid === null) {
            return [];
        }

        $dayCycle = $this->schoolDayCycleRepository->findOneBySchoolUuidAndDayCycleUuid(
            Uuid::fromString($schoolUuid),
            Uuid::fromString($dayCycleUuid)
        );

        if ($dayCycle === null) {
            throw new SchoolDayCycleNotFoundException();
        }

        return [$dayCycle];
    }
}
