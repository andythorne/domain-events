<?php

namespace AndyThorne\Components\DomainEventsBundle\EventProvider;

use AndyThorne\Components\DomainEventsBundle\Events\DomainEventInterface;
use AndyThorne\Components\DomainEventsBundle\Scheduler\DomainEventSchedulerInterface;

trait DomainEventProviderTrait
{
    /** @var DomainEventInterface[] */
    private $queuedDomainEvents = [];

    public function scheduleEvents(DomainEventSchedulerInterface $eventScheduler): void
    {
        foreach ($this->queuedDomainEvents as $event) {
            $eventScheduler->schedule($event);
        }

        $this->queuedDomainEvents = [];
    }

    protected function addDomainEvent(DomainEventInterface $event): void
    {
        $this->queuedDomainEvents[] = $event;
    }
}
