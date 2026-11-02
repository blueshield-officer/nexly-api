<?php

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Embeddable]
class Contact
{
    #[Groups(['embeddable:read'])]
    #[ORM\Column]
    public string $firstName;

    #[Groups(['embeddable:read'])]
    #[ORM\Column]
    public string $lastName;

    #[Groups(['embeddable:read'])]
    #[ORM\Column]
    public string $role;

    public static function fromArray(array $data): self
    {
        $contact = new self();
        $contact->firstName = $data['firstName'];
        $contact->lastName = $data['lastName'];
        $contact->role = $data['role'];
        return $contact;
    }
}
