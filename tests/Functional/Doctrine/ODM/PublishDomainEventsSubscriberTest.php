<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\Doctrine\ODM;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document\DomainEvents\DomainDocument;
use Tests\AndyThorne\Components\DomainEventsBundle\Functional\MessageBus\MessageInterceptorMiddleware;

class PublishDomainEventsSubscriberTest extends KernelTestCase
{
    private $messageBus;
    private $messageBusInterceptor;
    /** @var DocumentManager */
    private $objectManager;

    protected function setUp(): void
    {
        self::bootKernel([
            'environment' => 'odm',
        ]);

        $this->objectManager = self::$container->get('doctrine_mongodb')->getManager();
        $this->messageBus = self::$container->get(MessageInterceptorMiddleware::class);
        $this->messageBusInterceptor = self::$container->get(MessageInterceptorMiddleware::class);
    }

    protected function tearDown(): void
    {
        $this->objectManager->clear();
    }

    public function testCreateDomainDocument_FiresDomainEvent()
    {
        $domainDoc = new DomainDocument();
        $domainDoc->domainAction('1');
        $domainDoc->domainAction('2');
        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $this->assertCount(2, $this->messageBusInterceptor->messages);
    }

    public function testRemovedDomainDocument_FiresDomainEvent()
    {
        $domainDoc = new DomainDocument();
        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $domainDoc->domainAction('1');
        $domainDoc->domainAction('2');
        $this->objectManager->remove($domainDoc);
        $this->objectManager->flush();

        $this->assertCount(2, $this->messageBusInterceptor->messages);
    }

    public function testUpdateDomainDocument_FiresDomainEvent()
    {
        $domainDoc = new DomainDocument();

        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $domainDoc->domainAction('1');
        $domainDoc->domainAction('2');
        $this->objectManager->persist($domainDoc);
        $this->objectManager->flush();

        $this->assertCount(2, $this->messageBusInterceptor->messages);
    }
}
