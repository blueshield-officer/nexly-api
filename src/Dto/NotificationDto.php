<?php

namespace App\Dto;

use App\Entity\DistrictNotification;
use App\Entity\Notification;
use App\Entity\SchoolNotification;
use App\Enum\NotificationTypeEnum;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

final readonly class NotificationDto
{
    public function __construct(
        public Uuid                 $uuid,
        public NotificationTypeEnum $type,
        public string               $publishedAt,
        public string               $title,
        public string               $body,
    )
    {}

    static public function fromEntity(Notification $n): self
    {

        $type = match (true) {
            $n instanceof SchoolNotification   => NotificationTypeEnum::School,
            $n instanceof DistrictNotification => NotificationTypeEnum::District,
            default                            => NotificationTypeEnum::Website,
        };

        return new self(
            uuid: $n->getUuid(),
            type: $type,
            publishedAt: $n->getPublishedAt()->format(DateTimeInterface::ATOM),
            title: $n->getTitle(),
            body: $n->getBody()
        );
    }
}
