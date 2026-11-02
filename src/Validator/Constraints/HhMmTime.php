<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\RegexValidator;

#[Attribute]
class HhMmTime extends Regex
{
    public function __construct(?string $message = null)
    {
        parent::__construct(
            pattern: '/^([01]\d|2[0-3]):[0-5]\d$/',
            message: $message ?? 'Time must be in HH:MM format.'
        );
    }

    public function validatedBy(): string
    {
        return RegexValidator::class;
    }
}
