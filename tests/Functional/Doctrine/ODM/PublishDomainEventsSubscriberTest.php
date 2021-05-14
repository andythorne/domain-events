<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\Doctrine\ODM;

use AndyThorne\Components\DomainEventsBundle\Doctrine\ODM\DomainEventPublishingDocumentManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnTimeLimitListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Worker;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document\DomainDocument;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document\NonDomainDocument;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document\EventDispatcher\DomainActionEventLogSubscriber;

class PublishDomainEventsSubscriberTest extends KernelTestCase
{
    private TransportInterface $domainEventsTransport;
    private TransportInterface $otherTransport;
    private DocumentManager $objectManager;
    private EventDispatcherInterface $eventDispatcher;
    private MessageBusInterface $messageBus;
    private Worker $worker;

    protected function setUp(): void
    {
        self::bootKernel([
            'environment' => 'odm',
        ]);

        $this->objectManager = self::$container->get('doctrine_mongodb')->getManager();
        $this->messageBus = self::$container->get(MessageBusInterface::class);
        $this->eventDispatcher = self::$container->get(EventDispatcherInterface::class);
        $this->domainEventsTransport = self::$container->get('messenger.transport.test_transport');
        $this->otherTransport = self::$container->get('messenger.transport.other_transport');


        $this->eventDispatcher->addSubscriber(new StopWorkerOnTimeLimitListener(5));
        $this->worker = new Worker(
            [$this->domainEventsTransport],
            $this->messageBus,
            $this->eventDispatcher,
        );

        $this->objectManager->getDocumentCollection(DomainDocument::class)->drop();
        $this->objectManager->getDocumentCollection(NonDomainDocument::class)->drop();

        DomainActionEventLogSubscriber::$calledEvents = [];
    }

    protected function tearDown(): void
    {
        $this->objectManager->clear();
    }

    public function testCreateDomainDocumentFiresDomainEvent()
    {
        $domainDoc = new DomainDocument();
        $domainDoc->domainAction('1');
        $domainDoc->domainAction('2');
        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $this->eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(2));
        $this->worker->run();

        $allDomainDocs = $this->objectManager->getRepository(DomainDocument::class)->findAll();
        $allEventDocs = $this->objectManager->getRepository(NonDomainDocument::class)->findAll();

        $this->assertCount(1, $allDomainDocs);
        $this->assertCount(2, $allEventDocs);
        $this->assertCount(2, DomainActionEventLogSubscriber::$calledEvents);
    }

    public function testRemovedDomainDocumentFiresDomainEvent()
    {
        $domainDoc = new DomainDocument();
        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $domainDoc->domainAction('1');
        $domainDoc->domainAction('2');
        $this->objectManager->remove($domainDoc);
        $this->objectManager->flush();

        $this->eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(2));
        $this->worker->run();

        $allDomainDocs = $this->objectManager->getRepository(DomainDocument::class)->findAll();
        $allEventDocs = $this->objectManager->getRepository(NonDomainDocument::class)->findAll();

        $this->assertCount(0, $allDomainDocs);
        $this->assertCount(2, $allEventDocs);
        $this->assertCount(2, DomainActionEventLogSubscriber::$calledEvents);
    }

    public function testUpdateDomainDocumentFiresDomainEvent()
    {
        $domainDoc = new DomainDocument();

        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $domainDoc->domainAction('1');
        $domainDoc->domainAction('2');
        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $this->eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(2));
        $this->worker->run();

        $allDomainDocs = $this->objectManager->getRepository(DomainDocument::class)->findAll();
        $allEventDocs = $this->objectManager->getRepository(NonDomainDocument::class)->findAll();

        $this->assertCount(1, $allDomainDocs);
        $this->assertCount(2, $allEventDocs);
        $this->assertCount(2, DomainActionEventLogSubscriber::$calledEvents);
    }
}
