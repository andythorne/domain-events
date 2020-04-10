<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\fixtures\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document()
 */
class NonDomainDocument
{
    /** @ODM\Id() */
    private $id;
}
