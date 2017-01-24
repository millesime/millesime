<?php

namespace Millesime\Compiler\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Compiler\Compilation;
use Millesime\Compiler\Project;
use Millesime\Compiler\DistributionBuilder;

class CompilationTest extends TestCase
{
    public function testCompilation()
    {
        $project = new Project(__DIR__, null, ['name'=>'foobar', 'distrib' => [['name' => 'foo'], ['name' => 'bar']]]);

        $distribution = $this
            ->getMockBuilder('Millesime\Compiler\DistributionBuilder')
            ->disableOriginalConstructor()
            ->getMock()
        ;
//        $distribution->method('build')->willReturn($this->onConsecutiveCalls('foo', 'bar'));

        $compilation = new Compilation($project);
        $compilation->run($distribution);

    }
}
