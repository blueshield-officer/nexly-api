<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'district_notification')]
class DistrictNotification extends Notification
{
    #[ORM\ManyToOne(targetEntity: District::class)]
    #[ORM\JoinColumn(nullable: false)]
    private District $district;

    public function getDistrict(): District
    {
        return $this->district;
    }

    public function setDistrict(District $district): self
    {
        $this->district = $district;

        return $this;
    }
}
