<?php

namespace App\Entity;

use App\Repository\ExternalSatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExternalSatRepository::class)]
class ExternalSat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column]
    private ?bool $anticipated = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $testDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $registrationDeadline = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lateRegistrationDeadline = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAnticipated(): ?bool
    {
        return $this->anticipated;
    }

    public function setAnticipated(bool $anticipated): static
    {
        $this->anticipated = $anticipated;

        return $this;
    }

    public function getTestDate(): ?\DateTimeImmutable
    {
        return $this->testDate;
    }

    public function setTestDate(\DateTimeImmutable $testDate): static
    {
        $this->testDate = $testDate;

        return $this;
    }

    public function getRegistrationDeadline(): ?\DateTimeImmutable
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(?\DateTimeImmutable $registrationDeadline): static
    {
        $this->registrationDeadline = $registrationDeadline;

        return $this;
    }

    public function getLateRegistrationDeadline(): ?\DateTimeImmutable
    {
        return $this->lateRegistrationDeadline;
    }

    public function setLateRegistrationDeadline(?\DateTimeImmutable $lateRegistrationDeadline): static
    {
        $this->lateRegistrationDeadline = $lateRegistrationDeadline;

        return $this;
    }
}
