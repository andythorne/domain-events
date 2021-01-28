<?php

declare(strict_types=1);

namespace AndyThorne\Components\DomainEventsBundle\Events;

use DateTimeImmutable;

// phpcs:disable Symfony.NamingConventions.ValidClassName.InvalidAbstractName
abstract class DomainEvent implements DomainEventInterface
{
    use DomainEventTrait;

    public function __construct(?DateTimeImmutable $createdAt = null)
    {
        $this->createdAt = $createdAt ?: new DateTimeImmutable();
    }
}
