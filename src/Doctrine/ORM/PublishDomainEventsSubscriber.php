<?php

namespace AndyThorne\Components\DomainEventsBundle\Doctrine\ORM;

use AndyThorne\Components\DomainEventsBundle\Doctrine\AbstractDoctrineDomainEventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
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

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->addObject($args->getObject());
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $this->addObject($args->getObject());
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        $this->addObjectsFromUow($args->getEntityManager()->getUnitOfWork());
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->processDomainEvents();
    }
}
