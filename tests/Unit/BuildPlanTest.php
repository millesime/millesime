<?php

namespace Millesime\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Millesime\BuildPlan;
use Millesime\Release;
use Millesime\Manifest\PackageInfo;
use Symfony\Component\Finder\Finder;

class BuildPlanTest extends TestCase
{
    public function testBuildPlan()
    {
        $release = $this
            ->getMockBuilder(Release::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWorkingDirectory', 'getDestinationDirectory'])
            ->getMock()
        ;
        $release
            ->expects($this->any())
            ->method('getWorkingDirectory')
            ->willReturn('/source/path')
        ;
        $release
            ->expects($this->any())
            ->method('getDestinationDirectory')
            ->willReturn('/destination/path')
        ;

        $finder = $this
            ->getMockBuilder(Finder::class)
            ->getMock()
        ;

        $packageInfo = $this
            ->getMockBuilder(PackageInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFinder', 'getName'])
            ->getMock()
        ;
        $packageInfo
            ->expects($this->once())
            ->method('getFinder')
            ->with($release->getWorkingDirectory())
            ->willReturn($finder)
        ;
        $packageInfo
            ->expects($this->once())
            ->method('getName')
            ->willReturn('test.phar')
        ;

        $buildPlan = new BuildPlan($release, $packageInfo);
        $this->assertEquals($release, $buildPlan->getRelease());
        $this->assertEquals($packageInfo, $buildPlan->getPackageInfo());
        $this->assertInstanceOf(Finder::class, $buildPlan->getFiles());
        $this->assertEquals('/destination/path'.DIRECTORY_SEPARATOR.'test.phar', $buildPlan->getFileName());
    }
}