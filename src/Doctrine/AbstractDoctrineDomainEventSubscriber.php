<?php

declare(strict_types=1);

namespace AndyThorne\Components\DomainEventsBundle\Doctrine;

use AndyThorne\Components\DomainEventsBundle\EventProvider\DomainEventProviderInterface;
use AndyThorne\Components\DomainEventsBundle\Scheduler\DomainEventSchedulerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\PropertyChangedListener;

abstract class AbstractDoctrineDomainEventSubscriber implements EventSubscriber
{
    protected $domainEventScheduler;
    protected $domainObjects;

    public function __construct(DomainEventSchedulerInterface $domainEventScheduler)
    {
        $this->domainEventScheduler = $domainEventScheduler;
        $this->domainObjects = new ArrayCollection();
    }

    protected function addObjectsFromUow(PropertyChangedListener $unitOfWork): void
    {
        foreach ($unitOfWork->getIdentityMap() as $class => $objects) {
            if (!in_array(DomainEventProviderInterface::class, class_implements($class), true)) {
                continue;
            }

            foreach ($objects as $object) {
                if (!$this->domainObjects->contains($object)) {
                    $this->domainObjects->add($object);
                }
            }
        }
    }

    protected function processDomainEvents(): void
    {
        foreach ($this->domainObjects as $domainObject) {
            $domainObject->scheduleEvents($this->domainEventScheduler);
        }

        $this->domainEventScheduler->process();
    }

    protected function addObject($object): void
    {
        if ($object instanceof DomainEventProviderInterface && !$this->domainObjects->contains($object)) {
            $this->domainObjects->add($object);
        }
    }
}
