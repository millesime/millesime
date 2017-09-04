<?php

namespace Millesime\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Console\Application as SfApplication;
use Millesime\Application;
use Millesime\DependencyInjection\MillesimeExtension;

class ApplicationTest extends TestCase
{
    public function testRun()
    {
        $sfApplication = $this->createMock(SfApplication::class);
        $container = $this
            ->getMockBuilder(ContainerBuilder::class)
            ->setMethods(['get'])
            ->getMock()
        ;
        $container
            ->expects($this->once())
            ->method('get')
            ->with('application')
            ->willReturn($sfApplication)
        ;
        $extension = $this->createMock(MillesimeExtension::class);
        $extension->method('load')->willReturn($container);

        $application = new Application($container, $extension);
        $application->run();
    }
}
