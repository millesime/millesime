<?php

namespace Millesime\Compilation\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Compilation\Step;
use Millesime\Compilation\Steps;

class StepsTest extends TestCase
{
    public function testApply()
    {
        $phar = $this->createMock(\Phar::class);
        $options = ['foo' => 'bar'];

        $step = $this
            ->getMockBuilder(Step::class)
            ->setMethods(['execute'])
            ->getMock()
        ;
        $step
            ->expects($this->once())
            ->method('execute')
            ->with($phar, $options)
            ->willReturn($phar)
        ;

        $steps = new Steps();
        $steps->add($step);
        $result = $steps->apply($phar, $options);

        $this->assertEquals($phar, $result);
    }
}
