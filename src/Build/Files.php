<?php

namespace Millesime\Build;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Millesime\Event\CreatedPhar;

class Files
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

        $phar->startBuffering();
        foreach ($buildPlan->getFiles() as $file) {
            $localname = str_replace(
                $buildPlan->getRelease()->getWorkingDirectory(),
                null,
                $file
            );
            $localname = trim($localname, '\/.');
            if ($file->isDir()) {
                $phar->addEmptyDir($localname);
            } else {
                $phar->addFile($file, $localname);
            }
            $this->logger->debug($localname);
        }
        $phar->stopBuffering();
    }
}