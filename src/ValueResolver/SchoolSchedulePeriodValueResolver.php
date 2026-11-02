<?php

namespace App\ValueResolver;

use App\Entity\SchoolSchedulePeriod;
use App\Repository\SchoolSchedulePeriodRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

final readonly class SchoolSchedulePeriodValueResolver implements ValueResolverInterface
{
    public function __construct(
        private SchoolSchedulePeriodRepository $schoolSchedulePeriodRepository,
    ){}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== SchoolSchedulePeriod::class) {
            return [];
        }

        $schoolUuid = $request->attributes->get('schoolUuid');
        $scheduleUuid = $request->attributes->get('scheduleUuid');
        $schedulePeriodUuid = $request->attributes->get('schedulePeriodUuid');

        if ($schoolUuid === null || $scheduleUuid === null || $schedulePeriodUuid === null) {
            return [];
        }

        $schedulePeriod = $this->schoolSchedulePeriodRepository->findOneBySchoolUuidScheduleUuidAndPeriodUuidOrFail(
            Uuid::fromString($schoolUuid),
            Uuid::fromString($scheduleUuid),
            Uuid::fromString($schedulePeriodUuid)
        );

        return [$schedulePeriod];
    }
}
