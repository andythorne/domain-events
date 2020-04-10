<?php

namespace AndyThorne\Components\DomainEventsBundle\EventProvider;

use AndyThorne\Components\DomainEventsBundle\Events\DomainEventInterface;
use AndyThorne\Components\DomainEventsBundle\Scheduler\DomainEventSchedulerInterface;

trait DomainEventProviderTrait
{
    /** @var DomainEventInterface[] */
    private $domainEvents = [];

    public function scheduleEvents(DomainEventSchedulerInterface $eventScheduler): void
    {
        foreach ($this->domainEvents as $event) {
            $eventScheduler->schedule($event);
        }

        $this->domainEvents = [];
    }

    protected function queueDomainEvent(DomainEventInterface $event): void
    {
        $this->domainEvents[] = $event;
    }
}
