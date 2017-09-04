<?php

namespace Millesime\DependencyInjection\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\DependencyInjection\MillesimeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MillesimeExtensionTest extends TestCase
{
    public function testload()
    {
        $container = new ContainerBuilder();
        $extension = new MillesimeExtension();
        
        $container = $extension->load($container);

        $this->assertTrue($container->has('application'));
    }
}
