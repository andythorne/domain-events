<?php

declare(strict_types=1);

namespace AndyThorne\Components\DomainEventsBundle\EventProvider;

use AndyThorne\Components\DomainEventsBundle\Events\DomainEventInterface;
use AndyThorne\Components\DomainEventsBundle\Scheduler\DomainEventSchedulerInterface;

trait DomainEventProviderTrait
{
    /** @var DomainEventInterface[] */
    private array $queuedDomainEvents = [];

    public function scheduleEvents(DomainEventSchedulerInterface $eventScheduler): void
    {
        foreach ($this->queuedDomainEvents as $event) {
            $eventScheduler->schedule($event);
        }

        $this->queuedDomainEvents = [];
    }

    protected function record(DomainEventInterface $event): void
    {
        $this->queuedDomainEvents[] = $event;
    }
}
