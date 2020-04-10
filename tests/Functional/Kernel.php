<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional;

use AndyThorne\Components\DomainEventsBundle\DomainEventsBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new DomainEventsBundle();

        if ($this->getEnvironment() === 'odm') {
            yield new DoctrineMongoDBBundle();
        }

        if ($this->getEnvironment() === 'orm') {
            yield new DoctrineBundle();
        }
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.yaml');
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yaml');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
    }
}
