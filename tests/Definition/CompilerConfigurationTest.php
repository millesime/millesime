<?php

namespace Millesime\Definition\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Millesime\Definition\CompilerConfiguration;

class CompilerConfigurationTest extends TestCase
{
    public function testRun()
    {
        $config = ['name' => 'test', 'version' => 'dev-master'];

        $processor = new Processor();
        $configuration = new CompilerConfiguration();

        $processedConfiguration = $processor->processConfiguration(
            $configuration,
            [$config]
        );

        $this->assertTrue(is_array($processedConfiguration));
    }
}
