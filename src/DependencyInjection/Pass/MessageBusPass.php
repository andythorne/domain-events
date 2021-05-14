<?php

declare(strict_types=1);

namespace AndyThorne\Components\DomainEventsBundle\DependencyInjection\Pass;

use AndyThorne\Components\DomainEventsBundle\Scheduler\DomainEventScheduler;
use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MessageBusPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $messageBus = $container->getParameter('domain_events.message_bus');
        $domainEventScheduler = $container->getDefinition(DomainEventScheduler::class);

        if (!$container->hasDefinition($messageBus)) {
            throw new LogicException(sprintf('No service for the configures message bus "%s" could be found.', $messageBus));
        }

        $domainEventScheduler->setArgument('$messageBus', new Reference($messageBus));
    }
}
