<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Entity\EventDispatcher;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Entity\DomainEntityEventLog;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Entity\NonDomainEntity;

class DomainActionEventLogSubscriber implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    public static array $calledEvents = [];

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DomainEntityEventLog $domainActionEventLog)
    {
        self::$calledEvents[] = $domainActionEventLog;
        $newDoc = new NonDomainEntity();
        $this->entityManager->persist($newDoc);
        $this->entityManager->flush();
    }
}
