<?php

namespace Millesime\Compiler;

use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Processor;
use Millesime\Compiler\Definition\CompilerConfiguration;

class Project
{
    private $source;
    private $dest;
    private $config;
    private $logger;

    static public function manifest($source, $dest, $manifest, $logger = null)
    {
        $manifest_path = realpath($source).DIRECTORY_SEPARATOR.$manifest;

        if (!file_exists($manifest_path)) {
            throw new \Exception($manifest.' was not found');
        }

        $config = (array) json_decode(file_get_contents($manifest_path), true);

        if (!$logger) {
            $logger = new \Psr\Log\NullLogger();
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            $logger->critical("Error parsing configuration file {$manifest_path}");

            throw new \Exception("Error parsing configuration file {$manifest_path}");
        } else {
            $logger->debug("Loaded project configuration from {$manifest_path}", $config);
        }


        return new self($source, $dest, $config, $logger);
    }

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
        $git = new \Millesime\Compiler\Git\Version($this->source);

        $processor = new Processor();
        $configuration = $processor->processConfiguration(
            new CompilerConfiguration(),
            [
                ['version' => $git->getVersion()],
                $this->config
            ]
        );

        return $configuration;
    }
}
