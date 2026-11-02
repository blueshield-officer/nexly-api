<?php

namespace App\Entity;

use App\Repository\SchoolScheduleAdjustmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SchoolScheduleAdjustmentRepository::class)]
class SchoolScheduleAdjustment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SchoolSchedule $schoolSchedule = null;

    public function __construct(){
        $this->uuid = Uuid::v7();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSchoolSchedule(): ?SchoolSchedule
    {
        return $this->schoolSchedule;
    }

    public function setSchoolSchedule(?SchoolSchedule $schoolSchedule): static
    {
        $this->schoolSchedule = $schoolSchedule;

        return $this;
    }
}
