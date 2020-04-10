<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Entity\DomainEvents\DomainEntity;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\MessageBus\MessageInterceptorMiddleware;

class PublishDomainEventsSubscriberTest extends KernelTestCase
{
    private $messageBus;
    private $messageBusInterceptor;

    /** @var EntityManagerInterface */
    private $objectManager;

    protected function setUp(): void
    {
        self::bootKernel([
            'environment' => 'orm',
        ]);

        $this->objectManager = self::$container->get('doctrine')->getManager();
        $this->messageBus = self::$container->get(MessageInterceptorMiddleware::class);
        $this->messageBusInterceptor = self::$container->get(MessageInterceptorMiddleware::class);

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

        $this->assertCount(2, $this->messageBusInterceptor->messages);
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

        $this->assertCount(2, $this->messageBusInterceptor->messages);
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

        $this->assertCount(2, $this->messageBusInterceptor->messages);
    }
}
