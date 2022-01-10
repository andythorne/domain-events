<?php

declare(strict_types=1);

namespace AndyThorne\Components\DomainEventsBundle\Events;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

// phpcs:disable Symfony.NamingConventions.ValidClassName.InvalidAbstractName
abstract class DomainEvent implements DomainEventInterface
{
    use DomainEventTrait;

    protected Uuid $eventId;

    public function __construct(DateTimeImmutable $createdAt = null)
    {
        $this->eventId = Uuid::v4();
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }
}
