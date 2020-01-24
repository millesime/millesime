<?php

namespace Millesime;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Finder\Finder;
use Millesime\Manifest\PackageInfo;

/**
 * A BuildPlan tels what to do for each project package to your Compiler.
 * 
 * @author Thomas Gasc <thomas@gasc.fr>
 */
class BuildPlan
{
    private Release $release;
    private PackageInfo $packageInfo;
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param Release              $release
     * @param PackageInfo          $packageInfo
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Release $release,
        PackageInfo $packageInfo,
        LoggerInterface $logger = null
    ) {
        $this->release = $release;
        $this->packageInfo = $packageInfo;
        $this->logger = $logger ?: new NullLogger;
    }

    public function getRelease() : Release
    {
        return $this->release;
    }

    public function getPackageInfo() : PackageInfo
    {
        return $this->packageInfo;
    }

    /**
     * Returns all files that will be included in the package.
     *
     * @return Finder
     */
    public function getFiles() : Finder
    {
        return $this
            ->packageInfo
            ->getFinder($this->release->getWorkingDirectory())
        ;
    }

    /**
     * Returns the package file name.
     *
     * @return string
     */
    public function getFileName() : string 
    {
        return implode(
            DIRECTORY_SEPARATOR,
            [
                $this->release->getDestinationDirectory(),
                $this->packageInfo->getName()
            ]
        );
    }
}
