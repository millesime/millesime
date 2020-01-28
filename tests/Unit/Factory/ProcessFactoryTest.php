<?php

use PHPUnit\Framework\TestCase;

use Millesime\Factory\ProcessFactory;
use Symfony\Component\Process\Process;

class ProcessFactoryTest extends TestCase
{
    public function testConstruct()
    {
        $process = $this
            ->getMockBuilder(Process::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $callback = $this
            ->getMockBuilder(\stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock()
        ;
        $callback
            ->expects($this->once())
            ->method('__invoke')
            ->with('command', 'workingDirectory')
            ->willReturn($process)
        ;
    
        $processFactory = new ProcessFactory($callback);

        $this->assertEquals($process, $processFactory('command', 'workingDirectory'));
    }
}