<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Entity\DomainEvents;

use AndyThorne\Components\DomainEventsBundle\EventProvider\DomainEventProviderInterface;
use AndyThorne\Components\DomainEventsBundle\EventProvider\DomainEventProviderTrait;
use AndyThorne\Components\DomainEventsBundle\Events\DomainEvent;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class DomainEntity implements DomainEventProviderInterface
{
    use DomainEventProviderTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="string", nullable=true) */
    private $action;

    public function domainAction(string $action): void
    {
        $this->action = $action;
        $this->queueDomainEvent(new DomainEntityEvent($this, $action));
    }

    public function getAction(): ?string
    {
        return $this->action;
    }
}

class DomainEntityEvent extends DomainEvent
{
    /** @var DomainEntity */
    private $document;

    /** @var string */
    private $action;

    public function __construct(DomainEntity $document, string $action)
    {
        parent::__construct();

        $this->document = $document;
        $this->action = $action;
    }

    public function getDocument(): DomainEntity
    {
        return $this->document;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
