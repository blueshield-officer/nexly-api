<?php

namespace App\Entity;

use App\Entity\Embeddable\Contact;
use App\Entity\Embeddable\Location;
use App\Enum\RequestStatusEnum;
use App\Repository\DistrictRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: DistrictRequestRepository::class)]
#[ORM\Table(name: 'request_district')]
class DistrictRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 150)]
    private ?string $districtName = null;

    #[Embedded(class: Contact::class, columnPrefix: 'contact_')]
    public Contact $contact {
        get {
            return $this->contact;
        }
        set(Contact $value) {
            $this->contact = $value;
        }
    }

    #[Embedded(class: Location::class, columnPrefix: 'location_')]
    public Location $location {
        get {
            return $this->location;
        }
        set(Location $value) {
            $this->location = $value;
        }
    }

    #[ORM\Column(nullable: true)]
    private ?int $estimatedSchools = null;

    #[ORM\Column(type: "string", enumType: RequestStatusEnum::class)]
    private RequestStatusEnum $status = RequestStatusEnum::Pending;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adminNotes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $submittedAt = null;

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

    public function getDistrictName(): ?string
    {
        return $this->districtName;
    }

    public function setDistrictName(string $districtName): static
    {
        $this->districtName = $districtName;

        return $this;
    }

    public function getEstimatedSchools(): ?int
    {
        return $this->estimatedSchools;
    }

    public function setEstimatedSchools(?int $estimatedSchools): static
    {
        $this->estimatedSchools = $estimatedSchools;

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

    public function getAdminNotes(): ?string
    {
        return $this->adminNotes;
    }

    public function setAdminNotes(?string $adminNotes): static
    {
        $this->adminNotes = $adminNotes;

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
