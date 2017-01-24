<?php

namespace Methylbro\Compiler\Tests;

use PHPUnit\Framework\TestCase;
use Methylbro\Compiler\Compilation;
use Methylbro\Compiler\CompilationFactory;

class CompilationFactoryTest extends TestCase
{
    public function testCompilationFactory()
    {
        $project = $this
            ->getMockBuilder('Methylbro\Compiler\Project')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $factory = new CompilationFactory(Compilation::class);

        $compilation = $factory->create($project);

        $this->assertInstanceOf(Compilation::class, $compilation);
    }
}
