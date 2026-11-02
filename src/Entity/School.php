<?php

namespace App\Entity;

use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
#[ORM\HasLifecycleCallbacks]
class School
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $private = null;

    #[ORM\ManyToOne(inversedBy: 'schools')]
    #[ORM\JoinColumn(nullable: true)]
    private ?District $district = null;

    #[ORM\Column(length: 64, nullable: false, options: ['default' => 'America/New_York'])]
    private string $timezone = 'America/New_York';


    /**
     * @var Collection<int, SchoolColor>
     */
    #[ORM\OneToMany(targetEntity: SchoolColor::class, mappedBy: 'school')]
    private Collection $colors;

    /**
     * @var Collection<int, SchoolEmailDomain>
     */
    #[ORM\OneToMany(targetEntity: SchoolEmailDomain::class, mappedBy: 'school')]
    private Collection $email_domains;

    /**
     * @var Collection<int, SchoolSchedule>
     */
    #[ORM\OneToMany(targetEntity: SchoolSchedule::class, mappedBy: 'school')]
    private Collection $schedules;

    /**
     * @var Collection<int, SchoolCalendar>
     */
    #[ORM\OneToMany(targetEntity: SchoolCalendar::class, mappedBy: 'school')]
    private Collection $calendar;

    /**
     * @var Collection<int, SchoolGrade>
     */
    #[ORM\OneToMany(targetEntity: SchoolGrade::class, mappedBy: 'school')]
    #[ORM\OrderBy(['sortOrder' => 'ASC'])]
    private Collection $grades;

    #[ORM\ManyToOne]
    private ?Sis $sis = null;

    /**
     * @var Collection<int, SchoolSchedule>
     */
    #[ORM\OneToMany(targetEntity: SchoolSchedule::class, mappedBy: 'school')]
    private Collection $schoolSchedules;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->uuid = Uuid::v7();

        $this->colors = new ArrayCollection();
        $this->schedules = new ArrayCollection();
        $this->email_domains = new ArrayCollection();
        $this->calendar = new ArrayCollection();
        $this->schoolSchedules = new ArrayCollection();
        $this->grades = new ArrayCollection();

        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): static
    {
        $this->private = $private;

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

    public function getDistrictId(): ?int
    {
        return $this->district->getId();
    }

    public function getDistrict(): ?District
    {
        return $this->district;
    }

    public function setDistrict(?District $district): static
    {
        $this->district = $district;

        return $this;
    }

    /**
     * @return Collection<int, SchoolColor>
     */
    public function getColors(): Collection
    {
        return $this->colors;
    }

    public function addColor(SchoolColor $color): static
    {
        if (!$this->colors->contains($color)) {
            $this->colors->add($color);
            $color->setSchool($this);
        }

        return $this;
    }

    public function removeColor(SchoolColor $color): static
    {
        if ($this->colors->removeElement($color)) {
            // set the owning side to null (unless already changed)
            if ($color->getSchool() === $this) {
                $color->setSchool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SchoolSchedule>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(SchoolSchedule $schedule): static
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules->add($schedule);
            $schedule->setSchool($this);
        }

        return $this;
    }

    public function removeSchedule(SchoolSchedule $schedule): static
    {
        if ($this->schedules->removeElement($schedule)) {
            // set the owning side to null (unless already changed)
            if ($schedule->getSchool() === $this) {
                $schedule->setSchool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SchoolEmailDomain>
     */
    public function getEmailDomains(): Collection
    {
        return $this->email_domains;
    }

    public function addEmailDomain(SchoolEmailDomain $emailDomain): static
    {
        if (!$this->email_domains->contains($emailDomain)) {
            $this->email_domains->add($emailDomain);
            $emailDomain->setSchool($this);
        }

        return $this;
    }

    public function removeEmailDomain(SchoolEmailDomain $emailDomain): static
    {
        if ($this->email_domains->removeElement($emailDomain)) {
            // set the owning side to null (unless already changed)
            if ($emailDomain->getSchool() === $this) {
                $emailDomain->setSchool(null);
            }
        }

        return $this;
    }

    public function getSis(): ?Sis
    {
        return $this->sis;
    }

    public function setSis(?Sis $sis): static
    {
        $this->sis = $sis;

        return $this;
    }

    /**
     * @return Collection<int, SchoolCalendar>
     */
    public function getCalendar(): Collection
    {
        return $this->calendar;
    }

    public function addCalendar(SchoolCalendar $calendar): static
    {
        if (!$this->calendar->contains($calendar)) {
            $this->calendar->add($calendar);
            $calendar->setSchool($this);
        }

        return $this;
    }

    public function removeCalendar(SchoolCalendar $calendar): static
    {
        if ($this->calendar->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getSchool() === $this) {
                $calendar->setSchool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SchoolSchedule>
     */
    public function getSchoolSchedules(): Collection
    {
        return $this->schoolSchedules;
    }

    public function addSchoolSchedule(SchoolSchedule $schoolSchedule): static
    {
        if (!$this->schoolSchedules->contains($schoolSchedule)) {
            $this->schoolSchedules->add($schoolSchedule);
            $schoolSchedule->setSchool($this);
        }

        return $this;
    }

    public function removeSchoolSchedule(SchoolSchedule $schoolSchedule): static
    {
        if ($this->schoolSchedules->removeElement($schoolSchedule)) {
            // set the owning side to null (unless already changed)
            if ($schoolSchedule->getSchool() === $this) {
                $schoolSchedule->setSchool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SchoolGrade>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(SchoolGrade $grade): static
    {
        if (!$this->grades->contains($grade)) {
            $this->grades->add($grade);
            $grade->setSchool($this);
        }

        return $this;
    }

    public function removeGrade(SchoolGrade $grade): static
    {
        if ($this->grades->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getSchool() === $this) {
                $grade->setSchool(null);
            }
        }

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

}
