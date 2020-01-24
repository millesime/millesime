<?php

namespace Millesime\Build;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Millesime\Event\CreatedPhar;

class Metadata
{
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?: new NullLogger;
    }

    public function __invoke(CreatedPhar $event)
    {
        $buildPlan = $event->getBuildPlan();
        $phar = $event->getPhar();

        $phar->setMetadata([
            'release.date' => $buildPlan
                ->getRelease()
                ->getManifest()
                ->getDate()
                ->format(\DateTime::RFC3339_EXTENDED),
            'release.version' => $buildPlan
                ->getRelease()
                ->getManifest()
                ->getVersion(),
        ]);
    }
}