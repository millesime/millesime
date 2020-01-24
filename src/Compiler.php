<?php

namespace Millesime;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Process;
use Millesime\Factory\PharFactory;
use Millesime\Factory\PackageFactory;

/**
 * Compile your projects into phar archives.
 * 
 * @author Thomas Gasc <thomas@gasc.fr>
 */
class Compiler
{
    private PharFactory $pharFactory;
    private PackageFactory $packageFactory;
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param PharFactory          $pharFactory
     * @param PackageFactory       $packageFactory
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        PharFactory $pharFactory,
        PackageFactory $packageFactory,
        LoggerInterface $logger = null
    ) {
        $this->pharFactory = $pharFactory;
        $this->packageFactory = $packageFactory;
        $this->logger = $logger ?: new NullLogger;
    }

    /**
     * @param Release $release
     * @return Package[]
     */
    public function __invoke(Release $release) : Iterable
    {
        return array_map(
            [$this, 'build'],
            $release->getBuildsPlan(
                $this->logger
            )
        );
    }

    /**
     * @param BuildPlan $buildPlan
     * @return Package
     */
    public function build(BuildPlan $buildPlan) : Package
    {
        $phar = $this->pharFactory->__invoke($buildPlan);
        $package = $this->packageFactory->__invoke($buildPlan);

        return $package;
    }
}
