<?php

namespace Millesime;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Millesime\Manifest\Manifest;

/**
 * A Release describes a project who could compiled to phar.
 * 
 * @author Thomas Gasc <thomas@gasc.fr>
 */
class Release
{
    private string $workingDirectory;
    private string $destinationDirectory;
    private Manifest $manifest;

    /**
     * Constructor
     *
     * If no $destinationDirectory is specified, $workingDirectory will be used.
     *
     * @param Manifest    $manifest             Manifest of the project
     * @param string      $workingDirectory     Path of the project
     * @param string|null $destinationDirectory Path were the phar archives will be dropped
     */
    public function __construct(
        Manifest $manifest,
        string $workingDirectory,
        string $destinationDirectory = null
    ) {
        $this->manifest = $manifest;
        $this->workingDirectory = $workingDirectory;
        $this->destinationDirectory = $destinationDirectory ?: $workingDirectory;
    }

    public function getWorkingDirectory() : string
    {
        return $this->workingDirectory;
    }

    public function getDestinationDirectory() : string
    {
        return $this->destinationDirectory;
    }

    public function getManifest() : Manifest
    {
        return $this->manifest;
    }

    /**
     * @param LoggerInterface $logger
     * @return BuildPlan[]
     */
    public function getBuildsPlan(LoggerInterface $logger = null) : Iterable
    {
        $logger = $logger ?: new NullLogger;
        $buildsPlan = [];

        foreach ($this->manifest->getPackagesInfo() as $packageInfo) {
            $buildsPlan[] = new BuildPlan($this, $packageInfo, $logger);
        }

        return $buildsPlan;
    }
}
