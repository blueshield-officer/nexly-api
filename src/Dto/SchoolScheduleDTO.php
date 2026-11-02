<?php

namespace App\Dto;

readonly class SchoolScheduleDTO
{
    public function __construct(
        public string $code
    ){}

    static public function fromArray(array $data): self
    {
        return new self(
            code: $data['code']
        );
    }
}
