<?php

namespace App\ValueResolver;

use App\Dto\SchoolDto;
use App\Entity\SchoolSchedule;
use App\Repository\SchoolScheduleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

final readonly class SchoolScheduleValueResolver implements ValueResolverInterface
{
    public function __construct(
        private SchoolScheduleRepository $schoolScheduleRepository,
    ){}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== SchoolSchedule::class) {
            return [];
        }

        $schoolUuid = $request->attributes->get('schoolUuid');
        $scheduleUuid = $request->attributes->get('scheduleUuid');

        if ($schoolUuid === null || $scheduleUuid === null) {
            return [];
        }

        $schedule = $this->schoolScheduleRepository->findOneBySchoolUuidAndScheduleUuidOrFail(
            Uuid::fromString($schoolUuid),
            Uuid::fromString($scheduleUuid)
        );

        return [$schedule];
    }
}
