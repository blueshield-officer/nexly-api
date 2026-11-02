<?php

namespace App\Entity;

use App\Enum\SchoolCalendarEventTypeEnum;
use App\Repository\SchoolCalendarEventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolCalendarEventRepository::class)]
class SchoolCalendarEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    #[ORM\Column(type: 'string', enumType: SchoolCalendarEventTypeEnum::class)]
    private SchoolCalendarEventTypeEnum $type;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateStart = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateEnd = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $timeStart = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $timeEnd = null;

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


    public function getType(): ?SchoolCalendarEventTypeEnum
    {
        return $this->type;
    }

    public function setType(SchoolCalendarEventTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDateStart(): ?\DateTimeImmutable
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeImmutable $dateStart): static
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeImmutable
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeImmutable $dateEnd): static
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getTimeStart(): ?\DateTimeImmutable
    {
        return $this->timeStart;
    }

    public function setTimeStart(?\DateTimeImmutable $timeStart): static
    {
        $this->timeStart = $timeStart;

        return $this;
    }

    public function getTimeEnd(): ?\DateTimeImmutable
    {
        return $this->timeEnd;
    }

    public function setTimeEnd(?\DateTimeImmutable $timeEnd): static
    {
        $this->timeEnd = $timeEnd;

        return $this;
    }

}
