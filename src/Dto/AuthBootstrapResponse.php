<?php

namespace App\Dto;

final readonly class AuthBootstrapResponse
{
    public function __construct(
        public string              $jwt,
        public array               $preferences,
        public array               $states,
    )
    {}
}
