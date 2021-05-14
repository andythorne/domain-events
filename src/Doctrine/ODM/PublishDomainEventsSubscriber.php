<?php

declare(strict_types=1);

namespace AndyThorne\Components\DomainEventsBundle\Doctrine\ODM;

use AndyThorne\Components\DomainEventsBundle\Doctrine\AbstractDoctrineDomainEventSubscriber;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class PublishDomainEventsSubscriber extends AbstractDoctrineDomainEventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
            Events::preFlush,
            Events::postFlush,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->addObject($args->getObject());
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->addObject($args->getObject());
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $this->addObject($args->getObject());
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        $this->addObjectsFromUow($args->getObjectManager()->getUnitOfWork());
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->processDomainEvents();
    }
}
