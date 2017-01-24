<?php

namespace Methylbro\Compiler\Tests;

use PHPUnit\Framework\TestCase;
use Methylbro\Compiler\Compilation;
use Methylbro\Compiler\Project;
use Methylbro\Compiler\DistributionBuilder;

class CompilationTest extends TestCase
{
    public function testCompilation()
    {
        $project = new Project(__DIR__, null, ['name'=>'foobar', 'distrib' => [['name' => 'foo'], ['name' => 'bar']]]);

        $distribution = $this
            ->getMockBuilder('Methylbro\Compiler\DistributionBuilder')
            ->disableOriginalConstructor()
            ->getMock()
        ;
//        $distribution->method('build')->willReturn($this->onConsecutiveCalls('foo', 'bar'));

        $compilation = new Compilation($project);
        $compilation->run($distribution);

    }
}
