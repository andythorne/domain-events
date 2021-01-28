<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\Doctrine\ODM;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document\DomainDocument;

class PublishDomainEventsSubscriberTest extends KernelTestCase
{
    private InMemoryTransport $domainEventsTransport;
    private InMemoryTransport $otherTransport;
    private DocumentManager $objectManager;

    protected function setUp(): void
    {
        self::bootKernel([
            'environment' => 'odm',
        ]);

        $this->objectManager = self::$container->get('doctrine_mongodb')->getManager();
        $this->domainEventsTransport = self::$container->get('messenger.transport.test_transport');
        $this->otherTransport = self::$container->get('messenger.transport.other_transport');
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

        $this->assertCount(2, $this->domainEventsTransport->get());
        $this->assertCount(0, $this->otherTransport->get());
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

        $this->assertCount(2, $this->domainEventsTransport->get());
        $this->assertCount(0, $this->otherTransport->get());
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

        $this->assertCount(2, $this->domainEventsTransport->get());
        $this->assertCount(0, $this->otherTransport->get());
    }
}
