<?php

namespace Millesime\Compilation;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ProjectFactory
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string $class
     * @param LoggerInterface $logger
     */
    public function __construct($class = Project::class, LoggerInterface $logger = null)
    {
        $this->class = $class;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param string $source
     * @param string $dest
     * @param string $manifest
     * @return Project
     */
    public function create($source, $dest, $manifest)
    {
        return new $this->class(
            $source,
            $dest,
            $this->loadManifestConfig($source, $manifest),
            $this->logger
        );
    }

    /**
     * @param string $source
     * @param string $manifest
     * @return array
     * @throws \InvalidArgumentException
     * @throws \DomainException
     */
    private function loadManifestConfig($source, $manifest)
    {
        $path = realpath($source).DIRECTORY_SEPARATOR;
        $manifest_path = $path.$manifest;

        if (!is_file($manifest_path)) {
            throw new \InvalidArgumentException("No {$manifest} was found in '{$path}'.");
        }

        $config = (array) json_decode(file_get_contents($manifest_path), true);

        $error = json_last_error();
        if (JSON_ERROR_NONE !== $error) {
            throw new \DomainException("Error {$error} reading manifest '{$manifest_path}'.");
        }

        $this->logger->debug("Found {$manifest}", [$manifest_path]);

        return $config;
    }
}
