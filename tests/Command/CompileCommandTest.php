<?php

namespace Methylbro\Compiler\Command\Tests;

use PHPUnit\Framework\TestCase;
use Methylbro\Compiler\Command\CompileCommand;

class CompileCommandTest extends TestCase
{
    public function testCompileCommand()
    {
        $factory = $this
            ->getMockBuilder('Methylbro\Compiler\CompilationFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock()
        ;
        $compilation = $this
            ->getMockBuilder('Methylbro\Compiler\Compilation')
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock()
        ;
        $distribution = $this
            ->getMockBuilder('Methylbro\Compiler\DistributionBuilder')
            ->getMock()
        ;

        $factory
            ->method('create')
            ->willReturn($compilation)
        ;
        $compilation
            ->method('run')
            ->with($distribution)
        ;

        $input = $this
            ->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->getMock()
        ;
        $output = $this
            ->getMockBuilder('Symfony\Component\Console\Output\OutputInterface')
            ->getMock()
        ;

        $command = new CompileCommand($factory, $distribution);
        $command->run($input, $output);
    }
}
