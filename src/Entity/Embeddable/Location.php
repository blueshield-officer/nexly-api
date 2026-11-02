<?php

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Embeddable]
class Location
{
    #[Groups(['embeddable:read'])]
    #[ORM\Column]
    public string $city;

    #[Groups(['embeddable:read'])]
    #[ORM\Column]
    public string $state;

    #[Groups(['embeddable:read'])]
    #[ORM\Column(nullable: true)]
    public ?string $zip;

    #[Groups(['embeddable:read'])]
    #[ORM\Column(nullable: true)]
    public ?string $street;

    public static function fromArray(array $data): self
    {
        $location = new self();
        $location->city = $data['city'];
        $location->state = $data['state'];
        $location->zip = $data['zip'] ?? null;
        $location->street = $data['street'] ?? null;
        return $location;
    }
}
