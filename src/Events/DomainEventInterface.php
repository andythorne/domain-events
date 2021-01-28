<?php

declare(strict_types=1);

namespace AndyThorne\Components\DomainEventsBundle\Events;

use DateTimeImmutable;

interface DomainEventInterface
{
    public function getCreatedAt(): DateTimeImmutable;
}
