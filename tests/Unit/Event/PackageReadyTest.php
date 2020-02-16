<?php

namespace Millesime\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Millesime\Package;
use Millesime\BuildPlan;
use Millesime\Event\PackageReady;

class PackageReadyTest extends TestCase
{
    public function testConstruct()
    {
        $package = $this
            ->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $buildPlan = $this
            ->getMockBuilder(BuildPlan::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $packageReady = new PackageReady($package, $buildPlan);

        $this->assertEquals($package, $packageReady->getPackage());
        $this->assertEquals($buildPlan, $packageReady->getBuildPlan());
    }
}