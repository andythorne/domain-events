<?php

namespace AndyThorne\Components\DomainEventsBundle\Scheduler;

use AndyThorne\Components\DomainEventsBundle\Events\DomainEventInterface;

interface DomainEventSchedulerInterface
{
    public function schedule(DomainEventInterface $event): void;

    public function process(): void;
}
