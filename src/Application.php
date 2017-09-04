<?php

namespace Millesime;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Millesime\DependencyInjection\MillesimeExtension;

class Application
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @param ContainerBuilder $container
     * @param MillesimeExtension $extension
     */
    public function __construct(ContainerBuilder $container = null, MillesimeExtension $extension = null)
    {
        $container = $container ?: new ContainerBuilder();
        $extension = $extension ?: new MillesimeExtension();

        $this->container = $extension->load($container);
    }

    /**
     * @return int
     */
    public function run()
    {
        return $this->container->get('application')->run();
    }
}
