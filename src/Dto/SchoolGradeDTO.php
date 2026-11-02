<?php

namespace App\Dto;

class SchoolGradeDTO
{
    public function __construct(
        public string   $grade,
        public string   $name,
        public string   $color,
    ){}

    public static function fromArray(array $data): self
    {
        return new self(
            grade: $data['grade'],
            name: $data['name'],
            color: $data['color']
        );
    }
}
