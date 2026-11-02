<?php

namespace App\Entity;

use App\Repository\SchoolConfigRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolConfigRepository::class)]
class SchoolConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'schoolConfig')]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'default_schedule_id')]
    private ?SchoolSchedule $defaultSchedule = null;

    #[ORM\Column]
    private ?int $earlyDismissalThresholdMinutes = null;

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

    public function getDefaultSchedule(): ?SchoolSchedule
    {
        return $this->defaultSchedule;
    }

    public function setDefaultSchedule(?SchoolSchedule $defaultSchedule): static
    {
        $this->defaultSchedule = $defaultSchedule;

        return $this;
    }

    public function getEarlyDismissalThresholdMinutes(): ?int
    {
        return $this->earlyDismissalThresholdMinutes;
    }

    public function setEarlyDismissalThresholdMinutes(int $earlyDismissalThresholdMinutes): static
    {
        $this->earlyDismissalThresholdMinutes = $earlyDismissalThresholdMinutes;

        return $this;
    }
}
