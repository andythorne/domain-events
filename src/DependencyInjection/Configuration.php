<?php

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
            ->end()
        ;

        return $treeBuilder;
    }
}
