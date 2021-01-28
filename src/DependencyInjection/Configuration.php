<?php

declare(strict_types=1);

namespace AndyThorne\Components\DomainEventsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('domain_events');
        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('orm')->defaultValue(true)->end()
                ->booleanNode('odm')->defaultValue(false)->end()
                ->scalarNode('bus')->defaultValue('domain_events.bus')->end()
                ->scalarNode('transport')->defaultValue('domain_events.bus')->end()
                ->booleanNode('configure_routing')->defaultValue(true)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
