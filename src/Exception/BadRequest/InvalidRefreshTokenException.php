<?php

namespace App\Exception\BadRequest;

use App\Enum\ErrorMessageEnum;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class InvalidRefreshTokenException extends BadRequestHttpException
{
    public function __construct()
    {
        parent::__construct(ErrorMessageEnum::INVALID_REFRESH_TOKEN->value);
    }
}
