<?php

namespace App\Dto;

readonly class DistrictDTO
{

    public function __construct(
        public int $id,
        public string $uuid,
        public string $name,
    )
    {}

    public function jsonSerialize(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            uuid: $data['uuid'],
            name: $data['name'],
        );
    }
}
