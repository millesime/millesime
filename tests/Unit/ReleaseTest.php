<?php

use PHPUnit\Framework\TestCase;

use Psr\Log\LoggerInterface;
use Millesime\Release;
use Millesime\BuildPlan;
use Millesime\Manifest\Manifest;
use Millesime\Manifest\PackageInfo;

class ReleaseTest extends TestCase
{
    public function testRelease()
    {
        $packageInfo = $this
            ->getMockBuilder(PackageInfo::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $manifest = $this
            ->getMockBuilder(Manifest::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPackagesInfo'])
            ->getMock()
        ;
        $manifest
            ->expects($this->once())
            ->method('getPackagesInfo')
            ->willReturn([$packageInfo])
        ;

    	$release = new Release($manifest, '/source/path', '/destination/path');
    	$buildsPlan = $release->getBuildsPlan();

    	$this->assertEquals('/source/path', $release->getWorkingDirectory());
    	$this->assertEquals('/destination/path', $release->getDestinationDirectory());
    	$this->assertEquals($manifest, $release->getManifest());
    	$this->assertEquals($release, $buildsPlan[0]->getRelease());
    	$this->assertEquals($packageInfo, $buildsPlan[0]->getPackageInfo());
    }
}