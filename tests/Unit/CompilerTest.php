<?php

use PHPUnit\Framework\TestCase;

use Psr\Log\LoggerInterface;
use Millesime\Compiler;
use Millesime\Release;
use Millesime\BuildPlan;
use Millesime\Package;
use Millesime\PharFactory;
use Millesime\PackageFactory;

class CompilerTest extends TestCase
{
    public function testCompiler()
    {
        $buildPlan1 = $this
            ->getMockBuilder(BuildPlan::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $buildPlan2 = clone $buildPlan1;

        $phar1 = $this
            ->getMockBuilder(\Phar::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $phar2 = clone $phar1;

        $package1 = $this
            ->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $package2 = clone $package1;

        $pharFactory = $this
            ->getMockBuilder(PharFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['__invoke'])
            ->getMock()
        ;
        $pharFactory
            ->method('__invoke')
            ->will($this->returnValueMap([
                [$buildPlan1, $phar1], 
                [$buildPlan2, $phar2]
            ]))
        ;

        $packageFactory = $this
            ->getMockBuilder(PackageFactory::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $packageFactory
            ->method('__invoke')
            ->will($this->returnValueMap([
                [$buildPlan1, $package1], 
                [$buildPlan2, $package2]
            ]))
        ;

        $logger = $this
            ->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $release = $this
            ->getMockBuilder(Release::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBuildsPlan'])
            ->getMock()
        ;
        $release
            ->expects($this->once())
            ->method('getBuildsPlan')
            ->with($logger)
            ->willReturn([$buildPlan1, $buildPlan2])
        ;

        $compiler = new Compiler($pharFactory, $packageFactory, $logger);
        $packages = $compiler($release);

        $this->assertEquals([$package1, $package2], $packages);
    }
}