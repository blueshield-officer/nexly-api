<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class SchoolPeriodPatchRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;
}
