<?php

namespace Millesime\Command\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\OutputInterface;
use Millesime\Command\InitCommand;
use Millesime\Finder\FinderGenerator;

class InitCommandTest extends TestCase
{
    public function testExecute()
    {
        $generator = $this->createMock(FinderGenerator::class);

        $command = new InitCommand($generator);
        $tester = new CommandTester($command);
        $tester->execute(
            ['project' => 'test', 'distrib' => 'test'],
            ['interactive' => false]
        );
        $output = $tester->getDisplay();

        $this->assertEquals("The file millesime.json already exists.\n", $output);
    }
}
