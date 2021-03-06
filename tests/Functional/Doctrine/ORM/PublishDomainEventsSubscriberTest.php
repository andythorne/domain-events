<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Entity\DomainEntity;

class PublishDomainEventsSubscriberTest extends KernelTestCase
{
    /** @var InMemoryTransport */
    private $domainEventsTransport;

    /** @var InMemoryTransport */
    private $otherTransport;

    /** @var EntityManagerInterface */
    private $objectManager;

    protected function setUp(): void
    {
        self::bootKernel([
            'environment' => 'orm',
        ]);

        $this->objectManager = self::$container->get('doctrine')->getManager();
        $this->domainEventsTransport = self::$container->get('messenger.transport.test_transport');
        $this->otherTransport = self::$container->get('messenger.transport.other_transport');

        $metadatas = $this->objectManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->objectManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas, true);
    }

    public function testCreateDomainEntity_FiresDomainEvent()
    {
        $domainDoc = new DomainEntity();
        $domainDoc->domainAction('1');
        $domainDoc->domainAction('2');
        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $this->assertCount(2, $this->domainEventsTransport->get());
        $this->assertCount(0, $this->otherTransport->get());
    }

    public function testRemovedDomainEntity_FiresDomainEvent()
    {
        $domainDoc = new DomainEntity();
        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $domainDoc->domainAction('1');
        $domainDoc->domainAction('2');
        $this->objectManager->remove($domainDoc);
        $this->objectManager->flush();

        $this->assertCount(2, $this->domainEventsTransport->get());
        $this->assertCount(0, $this->otherTransport->get());
    }

    public function testUpdateDomainEntity_FiresDomainEvent()
    {
        $domainDoc = new DomainEntity();

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
