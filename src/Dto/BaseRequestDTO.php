<?php

namespace App\Dto;

use App\Enum\RequestStatusEnum;

readonly class BaseRequestDTO
{
    public function __construct(
        public null|string $firstName,
        public null|string $lastName,
        public null|string $role,

        public null|string $name,
        public null|string $city,
        public null|string $state,
        public null|string $zip,
        public null|string $street,

        public \DateTimeImmutable|null $submittedAt = null,
        public RequestStatusEnum | null $status = null,
    ) {}

    protected static function contactAndLocation(array $data): array
    {
        $contact = $data['contact'] ?? [];
        $location = $data['location'] ?? [];

        return [
            'firstName' => $contact['firstName'] ?? null,
            'lastName'  => $contact['lastName'] ?? null,
            'role'      => $contact['role'] ?? null,
            'name'      => $data['name'] ?? null,
            'city'      => $location['city'] ?? null,
            'state'     => $location['state'] ?? null,
            'zip'       => $location['zip'] ?? null,
            'street'    => $location['street'] ?? null,
        ];
    }

    public function validate(): array
    {
        $missing = [];

        if (empty($this->firstName)) $missing[] = 'firstName';
        if (empty($this->lastName))  $missing[] = 'lastName';
        if (empty($this->role))      $missing[] = 'role';
        if (empty($this->name))      $missing[] = 'name';
        if (empty($this->city))      $missing[] = 'city';
        if (empty($this->state))     $missing[] = 'state';

        return $missing;
    }

    public function toArray(): array
    {
        return [
            'contact' => [
                'firstName' => $this->firstName,
                'lastName'  => $this->lastName,
                'role'      => $this->role,
            ],
            'location' => [
                'city'   => $this->city,
                'state'  => $this->state,
                'zip'    => $this->zip,
                'street' => $this->street,
            ],
            'name'        => $this->name,
            'submittedAt' => $this->submittedAt?->format('Y-m-d H:i:s'),
            'status'      => $this->status?->value,
        ];
    }
}
