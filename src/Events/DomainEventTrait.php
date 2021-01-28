<?php

declare(strict_types=1);

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
