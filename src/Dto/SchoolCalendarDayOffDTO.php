<?php

namespace App\Dto;

readonly class SchoolCalendarDayOffDTO
{
    public function __construct(
        public array | null     $scope,
        public string | null    $title,
        public string | null    $description
    ){}

    static public function fromArray(array $data): self
    {
        return new self(
            scope: $data['scope'],
            title: $data['title'],
            description: $data['description']
        );
    }
}
