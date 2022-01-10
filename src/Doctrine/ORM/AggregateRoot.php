<?php

declare(strict_types=1);

namespace AndyThorne\Components\DomainEventsBundle\Doctrine\ORM;

use AndyThorne\Components\DomainEventsBundle\EventProvider\DomainEventProviderTrait;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Component\Uid\Uuid;

#[MappedSuperclass]
abstract class AggregateRoot
{
    use DomainEventProviderTrait;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    protected Uuid $id;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }
}
