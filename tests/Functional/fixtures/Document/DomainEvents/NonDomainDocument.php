<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document\DomainEvents;

use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

/**
 * @Document()
 */
class NonDomainDocument
{
    /** @Id() */
    private $id;
}
