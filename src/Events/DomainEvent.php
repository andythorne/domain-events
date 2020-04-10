<?php

namespace AndyThorne\Components\DomainEventsBundle\Events;

use DateTimeImmutable;

// phpcs:disable Symfony.NamingConventions.ValidClassName.InvalidAbstractName
abstract class DomainEvent implements DomainEventInterface
{
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
