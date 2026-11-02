<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraints\Regex;

#[Attribute]
class HexColor extends Regex
{
    public function __construct(?string $message = null)
    {
        parent::__construct(
            pattern: '/^#([0-9a-fA-F]{6}|[0-9a-fA-F]{3})$/',
            message: $message ?? 'Value must be a valid hex color (e.g. #FFF or #FFFFFF).'
        );
    }
}
