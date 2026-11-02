<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class FeedbackIdeaPostRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 1000)]
    public string $content;
}
