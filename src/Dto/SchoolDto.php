<?php

namespace App\Dto;

use App\Enum\SisProviderEnum;

readonly class SchoolDto
{
    public function __construct(
        public int                  $id,
        public string               $uuid,
        public string               $name,
        public bool                 $private,
        public string               $timezone,
        public int|null             $districtId,
        public array|null           $district,
        public array                $grades,
        public SchoolColorDTO       $colors,
        public SisProviderEnum|null $sis,
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            uuid: $data['uuid'],
            name: $data['name'],
            private: $data['private'],
            timezone: $data['timezone'],
            districtId: $data['districtId'] ?? null,
            district: $data['district'] ?? null,
            grades: array_map(fn($grade) => SchoolGradeDTO::fromArray($grade), $data['grades']),
            colors: self::mapColors($data['colors']),
            sis: $data['sis'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        $array = [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'private' => $this->private,
            'timezone' => $this->timezone,
            'grades' => $this->grades,
            'colors' => $this->colors,
            'sis' => $this->sis,
        ];

        if ($this->district) $array['district'] = $this->district;

        return $array;
    }

    private static function mapColors(array $colors): SchoolColorDTO
    {
        return SchoolColorDTO::fromArray($colors);
    }
}
