<?php

namespace AndyThorne\Components\DomainEventsBundle\Scheduler;

use AndyThorne\Components\DomainEventsBundle\Events\DomainEventInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DomainEventScheduler implements DomainEventSchedulerInterface
{
    private $messageBus;
    private $processing = false;

    /** @var DomainEventInterface[] */
    private $eventQueue = [];

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function schedule(DomainEventInterface $event): void
    {
        $this->eventQueue[] = $event;
    }

    public function process(): void
    {
        if ($this->processing) {
            return;
        }

        $this->processing = true;

        while ($this->size() > 0) {
            $processableQueue = $this->eventQueue;
            $this->eventQueue = [];

            $this->processEvents($processableQueue);
        }

        $this->processing = false;
    }

    public function size(): int
    {
        return count($this->eventQueue);
    }

    private function processEvents(array $events): void
    {
        uasort($events, function (DomainEventInterface $eventA, DomainEventInterface $eventB) {
            return $eventA->getCreatedAt() <=> $eventB->getCreatedAt();
        });

        foreach ($events as $event) {
            $this->messageBus->dispatch($event);
        }
    }
}
