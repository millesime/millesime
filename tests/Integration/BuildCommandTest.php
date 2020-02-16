<?php

namespace Millesime\Tests\Integration;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Millesime\Command\Build;

class BuildCommandTest extends IntegrationTestCase
{
    public function testExecute()
    {
        $application = new Application();
        $build = new Build();
        $application->add($build);

        $path = $this->installTestProject();

        $command = $application->find('build');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'source' => $path,
            'destination' => $path,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('test-millesime-1.phar', $output);
        $this->assertStringContainsString('test-millesime-2.phar', $output);
        $this->assertTrue(file_exists($path.'/test-millesime-1.phar'));
        $this->assertTrue(file_exists($path.'/test-millesime-2.phar'));
    }
}
