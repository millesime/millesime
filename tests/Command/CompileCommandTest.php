<?php

namespace Millesime\Compiler\Command\Tests;

use PHPUnit\Framework\TestCase;
use Millesime\Compiler\Command\CompileCommand;
use Symfony\Component\Console\Tester\CommandTester;

class CompileCommandTest extends TestCase
{
    public function testCompileCommand()
    {
        $factory = $this
            ->getMockBuilder('Millesime\Compiler\CompilationFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock()
        ;
        $compilation = $this
            ->getMockBuilder('Millesime\Compiler\Compilation')
            ->disableOriginalConstructor()
            ->setMethods(['run'])
            ->getMock()
        ;
        $distributionBuilder = $this
            ->getMockBuilder('Millesime\Compiler\DistributionBuilder')
            ->getMock()
        ;

        $factory
            ->method('create')
            ->willReturn($compilation)
        ;

        $command = new CompileCommand($factory, $distributionBuilder);

        $commandTester = new CommandTester($command);
        $commandTester->execute([], []);

        $output = $commandTester->getDisplay();
        $this->assertContains('Compilation completed', $output);
    }
}
