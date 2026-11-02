<?php

namespace App\Dto;

readonly class SchoolColorDTO
{
    public function __construct(
        public string $primary,
        public array  $secondaries,
    )
    {}

    static public function fromArray(array $data): self
    {
        return new self(
            primary: $data['primary'],
            secondaries: $data['secondaries']
        );
    }
}
