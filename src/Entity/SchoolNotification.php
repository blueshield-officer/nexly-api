<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'school_notification')]
class SchoolNotification extends Notification
{
    #[ORM\ManyToOne(targetEntity: School::class)]
    #[ORM\JoinColumn(nullable: false)]
    private School $school;

    public function getSchool(): School
    {
        return $this->school;
    }

    public function setSchool(School $school): static
    {
        $this->school = $school;

        return $this;
    }
}
