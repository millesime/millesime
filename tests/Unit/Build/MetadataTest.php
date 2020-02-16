<?php

namespace Millesime\Tests\Unit;

use \Phar;
use PHPUnit\Framework\TestCase;
use Millesime\BuildPlan;
use Millesime\Release;
use Millesime\Build\Metadata;
use Millesime\Manifest\Manifest;
use Millesime\Event\CreatedPhar;

class MetadataTest extends TestCase
{
    public function testMetadata()
    {
        $date = new \DateTime();
        $manifest = $this
            ->getMockBuilder(Manifest::class)
            ->disableOriginalConstructor()
            ->setMethods(['getVersion', 'getDate'])
            ->getMock()
        ;
        $manifest
            ->expects($this->once())
            ->method('getVersion')
            ->willReturn('foobar')
        ;
        $manifest
            ->expects($this->once())
            ->method('getDate')
            ->willReturn($date)
        ;

        $release = $this
            ->getMockBuilder(Release::class)
            ->disableOriginalConstructor()
            ->setMethods(['getManifest'])
            ->getMock()
        ;
        $release
            ->expects($this->any())
            ->method('getManifest')
            ->willReturn($manifest)
        ;

        $buildPlan = $this
            ->getMockBuilder(BuildPlan::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRelease'])
            ->getMock()
        ;
        $buildPlan
            ->expects($this->any())
            ->method('getRelease')
            ->willReturn($release)
        ;

        $phar = $this
            ->getMockBuilder(Phar::class)
            ->disableOriginalConstructor()
            ->setMethods(['setMetadata'])
            ->getMock()
        ;
        $phar
            ->expects($this->once())
            ->method('setMetadata')
            ->with([
                'release.date' => $date->format(\DateTime::RFC3339_EXTENDED),
                'release.version' => 'foobar',
            ])
        ;

        $createdPhar = new CreatedPhar($phar, $buildPlan);

        $metadata = new Metadata();
        $metadata($createdPhar);
    }
}
