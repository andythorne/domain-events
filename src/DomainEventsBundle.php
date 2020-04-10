<?php

namespace AndyThorne\Components\DomainEventsBundle;

use AndyThorne\Components\DomainEventsBundle\DependencyInjection\Pass\MessageBusPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DomainEventsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MessageBusPass());
    }
}
