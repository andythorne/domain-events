<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document\EventDispatcher;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document\DomainActionEventLog;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document\NonDomainDocument;

class DomainActionEventLogSubscriber implements MessageHandlerInterface
{
    private DocumentManager $documentManager;
    public static array $calledEvents = [];

    public function __construct(
        DocumentManager $documentManager
    ) {
        $this->documentManager = $documentManager;
    }

    public function __invoke(DomainActionEventLog $domainActionEventLog)
    {
        self::$calledEvents[] = $domainActionEventLog;
        $newDoc = new NonDomainDocument();
        $this->documentManager->persist($newDoc);
        $this->documentManager->flush();
    }
}
