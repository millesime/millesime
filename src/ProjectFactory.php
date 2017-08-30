<?php

namespace Millesime\Compiler;

class ProjectFactory
{
    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function create($source, $dest, $manifest)
    {
        $manifest_path = realpath($source).DIRECTORY_SEPARATOR.$manifest;

        if (!is_file($manifest_path)) {
            throw new \Exception($manifest.' was not found');
        }

        $config = (array) json_decode(file_get_contents($manifest_path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->critical("Error parsing configuration file {$manifest_path}");

            throw new \Exception("Error parsing configuration file {$manifest_path}");
        } 

        $this->logger->info("Found manifest in {$manifest_path}", $config);

        return new Project($source, $dest, $config, $this->logger);
    }
}
