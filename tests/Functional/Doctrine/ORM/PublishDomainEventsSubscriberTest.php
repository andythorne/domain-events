<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnTimeLimitListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Worker;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Entity\DomainEntity;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Entity\NonDomainEntity;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Entity\EventDispatcher\DomainActionEventLogSubscriber;

class PublishDomainEventsSubscriberTest extends KernelTestCase
{
    private TransportInterface $domainEventsTransport;
    private TransportInterface $otherTransport;
    private EntityManagerInterface $objectManager;
    private EventDispatcherInterface $eventDispatcher;
    private MessageBusInterface $messageBus;
    private Worker $worker;

    protected function setUp(): void
    {
        self::bootKernel([
            'environment' => 'orm',
        ]);

        $this->objectManager = self::$container->get('doctrine')->getManager();
        $this->messageBus = self::$container->get(MessageBusInterface::class);
        $this->eventDispatcher = self::$container->get(EventDispatcherInterface::class);
        $this->domainEventsTransport = self::$container->get('messenger.transport.test_transport');
        $this->otherTransport = self::$container->get('messenger.transport.other_transport');

        $metadatas = $this->objectManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->objectManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas, true);

        $this->eventDispatcher->addSubscriber(new StopWorkerOnTimeLimitListener(5));
        $this->worker = new Worker(
            [$this->domainEventsTransport],
            $this->messageBus,
            $this->eventDispatcher,
        );

        DomainActionEventLogSubscriber::$calledEvents = [];
    }

    public function testCreateDomainEntityFiresDomainEvent()
    {
        $domainDoc = new DomainEntity();
        $domainDoc->domainAction('1');
        $domainDoc->domainAction('2');
        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $this->eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(2));
        $this->worker->run();

        $allDomainDocs = $this->objectManager->getRepository(DomainEntity::class)->findAll();
        $allEventDocs = $this->objectManager->getRepository(NonDomainEntity::class)->findAll();

        $this->assertCount(2, DomainActionEventLogSubscriber::$calledEvents);
        $this->assertCount(1, $allDomainDocs);
        $this->assertCount(2, $allEventDocs);
    }

    public function testRemovedDomainEntityFiresDomainEvent()
    {
        $domainDoc = new DomainEntity();
        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $domainDoc->domainAction('1');
        $domainDoc->domainAction('2');
        $this->objectManager->remove($domainDoc);
        $this->objectManager->flush();

        $this->eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(2));
        $this->worker->run();

        $allDomainDocs = $this->objectManager->getRepository(DomainEntity::class)->findAll();
        $allEventDocs = $this->objectManager->getRepository(NonDomainEntity::class)->findAll();

        $this->domainEventsTransport->get();

        $this->assertCount(2, DomainActionEventLogSubscriber::$calledEvents);
        $this->assertCount(0, $allDomainDocs);
        $this->assertCount(2, $allEventDocs);
    }

    public function testUpdateDomainEntityFiresDomainEvent()
    {
        $domainDoc = new DomainEntity();

        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $domainDoc->domainAction('1');
        $domainDoc->domainAction('2');
        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $this->eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(2));
        $this->worker->run();

        $allDomainDocs = $this->objectManager->getRepository(DomainEntity::class)->findAll();
        $allEventDocs = $this->objectManager->getRepository(NonDomainEntity::class)->findAll();

        $events = $this->domainEventsTransport->get();

        $this->assertCount(2, DomainActionEventLogSubscriber::$calledEvents);
        $this->assertCount(1, $allDomainDocs);
        $this->assertCount(2, $allEventDocs);
    }
}
