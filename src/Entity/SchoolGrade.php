<?php

namespace App\Entity;

use App\Repository\SchoolGradeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SchoolGradeRepository::class)]
class SchoolGrade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 8)]
    private ?string $grade = null;

    #[ORM\Column(name: 'grade_name', length: 255, nullable: true)]
    private ?string $gradeName = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $hex = null;

    #[ORM\Column(name: 'sort_order')]
    private ?int $sortOrder = null;

    /**
     * @var Collection<int, SchoolCalendarDayOff>
     */
    #[ORM\ManyToMany(targetEntity: SchoolCalendarDayOff::class, mappedBy: 'scope')]
    private Collection $schoolCalendarDayOffs;

    public function __construct()
    {
        $this->schoolCalendarDayOffs = new ArrayCollection();
        $this->uuid = Uuid::v7();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrade(): ?string
    {
        return $this->grade;
    }

    public function setGrade(string $grade): static
    {
        $this->grade = $grade;

        return $this;
    }

    public function getGradeName(): ?string
    {
        return $this->gradeName;
    }

    public function setGradeName(?string $gradeName): static
    {
        $this->gradeName = $gradeName;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;

        return $this;
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

    /**
     * @return Collection<int, SchoolCalendarDayOff>
     */
    public function getSchoolCalendarDayOffs(): Collection
    {
        return $this->schoolCalendarDayOffs;
    }

    public function addSchoolCalendarDayOff(SchoolCalendarDayOff $schoolCalendarDayOff): static
    {
        if (!$this->schoolCalendarDayOffs->contains($schoolCalendarDayOff)) {
            $this->schoolCalendarDayOffs->add($schoolCalendarDayOff);
            $schoolCalendarDayOff->addScope($this);
        }

        return $this;
    }

    public function removeSchoolCalendarDayOff(SchoolCalendarDayOff $schoolCalendarDayOff): static
    {
        if ($this->schoolCalendarDayOffs->removeElement($schoolCalendarDayOff)) {
            $schoolCalendarDayOff->removeScope($this);
        }

        return $this;
    }

    public function getHex(): ?string
    {
        return $this->hex;
    }

    public function setHex(?string $hex): static
    {
        $this->hex = $hex;

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
}
