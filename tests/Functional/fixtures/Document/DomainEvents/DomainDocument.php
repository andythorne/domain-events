<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document\DomainEvents;

use AndyThorne\Components\DomainEventsBundle\EventProvider\DomainEventProviderInterface;
use AndyThorne\Components\DomainEventsBundle\EventProvider\DomainEventProviderTrait;
use AndyThorne\Components\DomainEventsBundle\Events\DomainEvent;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

/**
 * @Document()
 */
class DomainDocument implements DomainEventProviderInterface
{
    use DomainEventProviderTrait;

    /** @Id() */
    private $id;

    /** @Field(type="string") */
    private $action;

    public function domainAction(string $action): void
    {
        $this->action = $action;
        $this->queueDomainEvent(new DomainActionEvent($this, $action));
    }

    public function getAction(): ?string
    {
        return $this->action;
    }
}

class DomainActionEvent extends DomainEvent
{
    /** @var DomainDocument */
    private $document;

    /** @var string */
    private $action;

    public function __construct(DomainDocument $document, string $action)
    {
        parent::__construct();

        $this->document = $document;
        $this->action = $action;
    }

    public function getDocument(): DomainDocument
    {
        return $this->document;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
