<?php

namespace Millesime\Compiler\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Compiler\Compilation;
use Millesime\Compiler\CompilationFactory;

class CompilationFactoryTest extends TestCase
{
    public function testCompilationFactory()
    {
        $project = $this
            ->getMockBuilder('Millesime\Compiler\Project')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $factory = new CompilationFactory(Compilation::class);

        $compilation = $factory->create($project);

        $this->assertInstanceOf(Compilation::class, $compilation);
    }
}
