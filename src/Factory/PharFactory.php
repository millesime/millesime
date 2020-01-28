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
    private EventDispatcher $dispatcher;
    private LoggerInterface $logger;
    private $checkExistingPharMethod;
    private $deleteExistingPharMethod;

    /**
     * @param EventDispatcher      $dispatcher
     * @param LoggerInterface|null $logger
     * @param callable             $checkExistingPharMethod
     * @param callable             $deleteExistingPharMethod
     */
    public function __construct(
        EventDispatcher $dispatcher,
        LoggerInterface $logger = null,
        callable $checkExistingPharMethod = null,
        callable $deleteExistingPharMethod = null
    ) {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger ?: new NullLogger;
        $this->checkExistingPharMethod = $checkExistingPharMethod ?: 'file_exists'; 
        $this->deleteExistingPharMethod = $deleteExistingPharMethod ?: ['Phar', 'unlinkArchive'];
    }

    public function __invoke(BuildPlan $buildPlan) : Phar
    {
        if (call_user_func($this->checkExistingPharMethod, $buildPlan->getFileName())) {
            /**
             * Do not simply use unlink() :
             * @see https://bugs.php.net/bug.php?id=69323
             * @see https://www.php.net/manual/fr/phar.unlinkarchive.php
             */
            call_user_func(
                $this->deleteExistingPharMethod, 
                $buildPlan->getFileName()
            );
        }

        $phar = new Phar($buildPlan->getFileName());
        $createdPhar = new CreatedPhar($phar, $buildPlan);

        $this->dispatcher->dispatch($createdPhar, CreatedPhar::EVENT_NAME);

        return $phar;
    }
}
