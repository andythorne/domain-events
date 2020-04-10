<?php

namespace AndyThorne\Components\DomainEventsBundle\EventProvider;

use AndyThorne\Components\DomainEventsBundle\Scheduler\DomainEventSchedulerInterface;

interface DomainEventProviderInterface
{
    public function scheduleEvents(DomainEventSchedulerInterface $eventScheduler): void;
}
