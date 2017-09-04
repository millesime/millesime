<?php

namespace Millesime\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Compiler;
use Millesime\Compilation\Project;
use Millesime\Compilation\Compilation;
use Millesime\Compilation\ProjectFactory;
use Millesime\Compilation\CompilationFactory;
use Millesime\Compilation\DistributionBuilder;

class CompilerTest extends TestCase
{
    public function testExecute()
    {
        $source = 'a';
        $dest = 'b';
        $manifest = 'c';

        $project = $this->createMock(Project::class);

        $projectFactory = $this
            ->getMockBuilder(ProjectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock()
        ;
        $projectFactory
            ->expects($this->once())
            ->method('create')
            ->with($source, $dest, $manifest)
            ->willReturn($project)
        ;

        $distributionBuilder = $this->createMock(DistributionBuilder::class);

        $compilation = $this
            ->getMockBuilder(Compilation::class)
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock()
        ;
        $compilation
            ->expects($this->once())
            ->method('run')
            ->with($distributionBuilder)
        ;

        $compilationFactory = $this
            ->getMockBuilder(CompilationFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock()
        ;
        $compilationFactory
            ->expects($this->once())
            ->method('create')
            ->with($project)
            ->willReturn($compilation)
        ;

        $compiler = new Compiler($projectFactory, $compilationFactory, $distributionBuilder);
        $compiler->execute($source, $dest, $manifest);
    }
}
