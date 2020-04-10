<?php

namespace AndyThorne\Components\DomainEventsBundle\Events;

use DateTimeImmutable;

interface DomainEventInterface
{
    public function getCreatedAt(): DateTimeImmutable;
}
