<?php

namespace App\Exception\NotFound;

use App\Enum\ErrorMessageEnum;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SchoolNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct(ErrorMessageEnum::SCHOOL_NOT_FOUND->value);
    }
}
