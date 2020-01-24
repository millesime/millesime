<?php

namespace Millesime\Factory;

use \Phar;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Millesime\BuildPlan;
use Millesime\Event\CreatedPhar;

class PharFactory
{
    private LoggerInterface $logger;
    private EventDispatcher $dispatcher;

    /**
     * @param EventDispatcher      $dispatcher
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        EventDispatcher $dispatcher,
        LoggerInterface $logger = null
    ) {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger ?: new NullLogger;
    }

    public function __invoke(BuildPlan $buildPlan) : Phar
    {
        if (file_exists($buildPlan->getFileName())) {
            #unlink($buildPlan->getFileName());
            /**
             * @see https://bugs.php.net/bug.php?id=69323
             * @see https://www.php.net/manual/fr/phar.unlinkarchive.php
             */
            Phar::unlinkArchive($buildPlan->getFileName());
        }

        $phar = new Phar($buildPlan->getFileName());
        $createdPhar = new CreatedPhar($phar, $buildPlan);

        $this->dispatcher->dispatch($createdPhar, CreatedPhar::EVENT_NAME);

        return $phar;
    }
}
