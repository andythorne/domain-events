<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document;

use AndyThorne\Components\DomainEventsBundle\EventProvider\DomainEventProviderInterface;
use AndyThorne\Components\DomainEventsBundle\EventProvider\DomainEventProviderTrait;
use AndyThorne\Components\DomainEventsBundle\Events\DomainEvent;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document()
 */
class DomainDocument implements DomainEventProviderInterface
{
    use DomainEventProviderTrait;

    /** @ODM\Id() */
    private $id;

    /** @ODM\Field(type="string") */
    private $action;

    public function domainAction(string $action): void
    {
        $this->action = $action;
        $this->record(new DomainActionEventLog($this, $action));
    }
}

class DomainActionEventLog extends DomainEvent
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
}
