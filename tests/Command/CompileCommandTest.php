<?php

namespace Millesime\Command\Tests;

use PHPUnit\Framework\TestCase;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\OutputInterface;
use Millesime\Compiler;
use Millesime\Command\CompileCommand;

class CompileCommandTest extends TestCase
{
    public function testExecute()
    {
        $compiler = $this
            ->getMockBuilder(Compiler::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock()
        ;
        $compiler
            ->expects($this->once())
            ->method('execute')
            ->with('./', getcwd(), 'millesime.json')
        ;
        $handler = $this
            ->getMockBuilder(ErrorLogHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['setLevel'])
            ->getMock()
        ;
        $handler
            ->expects($this->once())
            ->method('setLevel')
            ->with(Logger::WARNING)
        ;

        $command = new CompileCommand($compiler, $handler);
        $tester = new CommandTester($command);
        $tester->execute(
            ['source' => './'],
            ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]
        );
        $output = $tester->getDisplay();

        $this->assertEquals("Compilation completed\n", $output);
    }
}
