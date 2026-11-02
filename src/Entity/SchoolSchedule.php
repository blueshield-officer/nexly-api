<?php

namespace App\Entity;

use App\Repository\SchoolScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SchoolScheduleRepository::class)]
class SchoolSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'schoolSchedules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalId = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, SchoolSchedulePeriod>
     */
    #[ORM\OneToMany(targetEntity: SchoolSchedulePeriod::class, mappedBy: 'schedule')]
    #[ORM\OrderBy(['startTime' => 'ASC'])]
    private Collection $schoolSchedulePeriods;

    #[ORM\Column]
    private ?bool $isHidden = null;

    public function __construct()
    {
        $this->schoolSchedulePeriods = new ArrayCollection();
        $this->uuid = Uuid::v7();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): static
    {
        $this->school = $school;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, SchoolSchedulePeriod>
     */
    public function getSchoolSchedulePeriods(): Collection
    {
        return $this->schoolSchedulePeriods;
    }

    public function addSchoolSchedulePeriod(SchoolSchedulePeriod $schoolSchedulePeriod): static
    {
        if (!$this->schoolSchedulePeriods->contains($schoolSchedulePeriod)) {
            $this->schoolSchedulePeriods->add($schoolSchedulePeriod);
            $schoolSchedulePeriod->setSchedule($this);
        }

        return $this;
    }

    public function removeSchoolSchedulePeriod(SchoolSchedulePeriod $schoolSchedulePeriod): static
    {
        if ($this->schoolSchedulePeriods->removeElement($schoolSchedulePeriod)) {
            // set the owning side to null (unless already changed)
            if ($schoolSchedulePeriod->getSchedule() === $this) {
                $schoolSchedulePeriod->setSchedule(null);
            }
        }

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function isHidden(): ?bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(bool $isHidden): static
    {
        $this->isHidden = $isHidden;

        return $this;
    }
}
