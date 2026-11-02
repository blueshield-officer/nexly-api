<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class SchoolDayCyclePostRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 5)]
    public string $code;

    public int $position;
}
