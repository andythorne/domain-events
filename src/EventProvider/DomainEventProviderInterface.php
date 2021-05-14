<?php

declare(strict_types=1);

namespace AndyThorne\Components\DomainEventsBundle\EventProvider;

use AndyThorne\Components\DomainEventsBundle\Scheduler\DomainEventSchedulerInterface;

interface DomainEventProviderInterface
{
    public function scheduleEvents(DomainEventSchedulerInterface $eventScheduler): void;
}
