<?php

namespace AndyThorne\Components\DomainEventsBundle\Events;

use DateTimeImmutable;

trait DomainEventTrait
{
    private $createdAt;

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
