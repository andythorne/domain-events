<?php

declare(strict_types=1);

namespace AndyThorne\Components\DomainEventsBundle\DependencyInjection;

use AndyThorne\Components\DomainEventsBundle\Events\DomainEventInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DomainEventsExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        // process the configuration of AcmeHelloExtension
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $frameworkConfig = [
            'messenger' => [
                'buses' => [
                    $config['bus'] => [
                        'default_middleware' => 'allow_no_handlers',
                    ],
                ],
            ],
        ];

        if ($config['configure_routing']) {
            $frameworkConfig['messenger']['routing'] = [
                DomainEventInterface::class => $config['transport'],
            ];
        }

        $container->prependExtensionConfig('framework', $frameworkConfig);

        if ($config['orm']) {
            $container->prependExtensionConfig('framework', $frameworkConfig);
        }
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yml');

        if ($mergedConfig['orm']) {
            $loader->load('orm.yml');
        }

        if ($mergedConfig['odm']) {
            $loader->load('odm.yml');
        }

        $container->setParameter('domain_events.message_bus', $mergedConfig['bus']);
    }
}
