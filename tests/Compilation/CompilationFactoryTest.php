<?php

namespace Millesime\Compilation\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Compilation\Project;
use Millesime\Compilation\Compilation;
use Millesime\Compilation\CompilationFactory;

class CompilationFactoryTest extends TestCase
{
    public function testCreate()
    {
        $project = $this->createMock(Project::class);

        $factory = new CompilationFactory();
        $compilation = $factory->create($project);

        $this->assertInstanceOf(Compilation::class, $compilation);
    }
}
