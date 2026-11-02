<?php

namespace App\Entity;

use App\Entity\Embeddable\Contact;
use App\Entity\Embeddable\Location;
use App\Enum\RequestStatusEnum;
use App\Repository\SchoolRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SchoolRequestRepository::class)]
#[ORM\Table(name: 'request_school')]
class SchoolRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Groups(['request:read'])]
    #[ORM\Column(length: 150)]
    private ?string $schoolName = null;

    #[Groups(['request:read'])]
    #[Embedded(class: Contact::class, columnPrefix: 'contact_')]
    public Contact $contact {
        get {
            return $this->contact;
        }
        set(Contact $value) {
            $this->contact = $value;
        }
    }

    #[Groups(['request:read'])]
    #[Embedded(class: Location::class, columnPrefix: 'location_')]
    public Location $location {
        get {
            return $this->location;
        }
        set(Location $value) {
            $this->location = $value;
        }
    }

    #[Groups(['request:read'])]
    #[ORM\Column(type: "string", enumType: RequestStatusEnum::class)]
    private RequestStatusEnum $status = RequestStatusEnum::Pending;

    #[Groups(['request:read'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $submittedAt = null;

    #[Groups(['request:read'])]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $reviewedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSchoolName(): ?string
    {
        return $this->schoolName;
    }

    public function setSchoolName(string $schoolName): static
    {
        $this->schoolName = $schoolName;

        return $this;
    }

    public function getStatus(): RequestStatusEnum
    {
        return $this->status;
    }

    public function setStatus(RequestStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSubmittedAt(): ?\DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(\DateTimeImmutable $submittedAt): static
    {
        $this->submittedAt = $submittedAt;

        return $this;
    }

    public function getReviewedAt(): ?\DateTimeImmutable
    {
        return $this->reviewedAt;
    }

    public function setReviewedAt(?\DateTimeImmutable $reviewedAt): static
    {
        $this->reviewedAt = $reviewedAt;

        return $this;
    }
}
