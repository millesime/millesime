<?php

namespace Millesime\Compiler;

use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Processor;
use Millesime\Compiler\Definition\CompilerConfiguration;
use Millesime\Compiler\Git\Version;

class Project
{
    private $source;
    private $dest;
    private $config;
    private $logger;

    public function __construct($source, $dest = null, array $config = [], LoggerInterface $logger = null)
    {
        if (!$logger) {
            $this->logger = new \Psr\Log\NullLogger();
        }

        $this->source = $source;
        $this->dest = $dest ? $dest : $source;
        $this->config = $config;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getDestination()
    {
        return $this->dest;
    }

    public function getConfig()
    {
        $version = new Version();
        $processor = new Processor();

        $configuration = $processor->processConfiguration(
            new CompilerConfiguration(),
            [
                ['version' => $version->resolve($this->source)],
                $this->config
            ]
        );

        return $configuration;
    }
}
