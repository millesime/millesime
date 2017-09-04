<?php

namespace Millesime\Compilation\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Compilation\Project;
use Millesime\Compilation\Compilation;
use Millesime\Compilation\DistributionBuilder;

class CompilationTest extends TestCase
{
    public function testCompilationWithDisabledPharWriteAccess()
    {
        $cantWrite = function () {
            return false;
        };
        $project = $this->createMock(Project::class);

        $this->expectException(\RuntimeException::class);

        $compilation = new Compilation($project, null, $cantWrite);
    }

    public function testRun()
    {
        $source = 'source';
        $destination = 'destination';
        $projectConfig = [
            'distrib' => [
                ['name' => 'foo'],
                ['name' => 'bar'],
            ],
        ];
        $phar = $this->createMock(\Phar::class);
        $project = $this
            ->getMockBuilder(Project::class)
            ->disableOriginalConstructor()
            ->setMethods(['getConfig', 'getSource', 'getDestination'])
            ->getMock()
        ;
        $project
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($projectConfig)
        ;
        $project
            ->method('getSource')
            ->willReturn($source)
        ;
        $project
            ->method('getDestination')
            ->willReturn($destination)
        ;

        $builder = $this
            ->getMockBuilder(DistributionBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock()
        ;
        $builder
            ->expects($this->exactly(2))
            ->method('build')
            ->willReturn($phar)
        ;

        $compilation = new Compilation($project);
        $result = $compilation->run($builder);

        $this->assertEquals(['foo' => $phar, 'bar' => $phar], $result);
    }
}
