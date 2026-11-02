<?php

namespace App\Entity;

use App\Repository\SchoolSchedulePeriodRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SchoolSchedulePeriodRepository::class)]
class SchoolSchedulePeriod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid;

    #[ORM\ManyToOne(inversedBy: 'schoolSchedulePeriods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SchoolSchedule $schedule = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SchoolPeriod $period = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $endTime = null;

    public function __construct(){
        $this->uuid = Uuid::v7();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSchedule(): ?SchoolSchedule
    {
        return $this->schedule;
    }

    public function setSchedule(?SchoolSchedule $schedule): static
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeImmutable $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getPeriod(): ?SchoolPeriod
    {
        return $this->period;
    }

    public function setPeriod(?SchoolPeriod $period): static
    {
        $this->period = $period;

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
