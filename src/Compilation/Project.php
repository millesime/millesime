<?php

namespace Millesime\Compilation;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Config\Definition\Processor;
use Millesime\Definition\CompilerConfiguration;
use Millesime\Git\Version;

class Project
{
    private $source;
    private $dest;
    private $config;
    private $logger;

    public function __construct($source, $dest = null, array $config = [], LoggerInterface $logger = null)
    {
        $this->source = $source;
        $this->dest = $dest ?: $source;
        $this->config = $config;
        $this->logger = $logger ?: new NullLogger();
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
