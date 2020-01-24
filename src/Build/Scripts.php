<?php

namespace Millesime\Build;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Millesime\Factory\ProcessFactory;
use Millesime\Event\CreatedPhar;

class Scripts
{
    private ProcessFactory $processFactory;
    private bool $noScripts;
    private LoggerInterface $logger;

    /**
     * @param ProcessFactory       $processFactory
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ProcessFactory $processFactory,
        bool $noScripts = false,
        LoggerInterface $logger = null
    ) {
        $this->processFactory = $processFactory;
        $this->noScripts = $noScripts;
        $this->logger = $logger ?: new NullLogger;
    }

    public function __invoke(CreatedPhar $event)
    {
        $buildPlan = $event->getBuildPlan();
        $logger = $this->logger;

        foreach ($buildPlan->getPackageInfo()->getScripts() as $script) {
            $logger->notice($script);
            $process = $this->processFactory->__invoke(
                $script,
                $buildPlan->getRelease()->getWorkingDirectory()
            );
            if (!$this->noScripts) {
                $process->run(function ($type, $buffer) use ($logger) {
                    $logger->debug($buffer);
                });
            }
        }
    }
}
