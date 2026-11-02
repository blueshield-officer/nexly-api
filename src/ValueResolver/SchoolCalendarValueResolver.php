<?php

namespace App\ValueResolver;

use App\Entity\SchoolCalendar;
use App\Repository\SchoolCalendarRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

final readonly class SchoolCalendarValueResolver implements ValueResolverInterface
{
    public function __construct(
        private SchoolCalendarRepository $schoolCalendarRepository,
    ){}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== SchoolCalendar::class) {
            return [];
        }

        $schoolUuid = $request->attributes->get('schoolUuid');
        $dateYmd = $request->attributes->get('ymd');

        if ($schoolUuid === null || $dateYmd === null) {
            return [];
        }

        $dateObject = \DateTimeImmutable::createFromFormat('Y-m-d', $dateYmd);
        if ($dateObject === false) {
            throw new BadRequestException('Invalid date format');

        }

        $calendarDay = $this->schoolCalendarRepository->findOneBySchoolUuidAndDate(
            Uuid::fromString($schoolUuid),
            $dateObject
        );

        if ($calendarDay === null) {
            throw new NotFoundHttpException('Calendar day not found for this school.');
        }

        return [$calendarDay];
    }
}
