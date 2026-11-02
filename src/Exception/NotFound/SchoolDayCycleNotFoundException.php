<?php

namespace App\Exception\NotFound;

use App\Enum\ErrorMessageEnum;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SchoolDayCycleNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct(ErrorMessageEnum::SCHOOL_DAY_CYCLE_NOT_FOUND->value);
    }
}
