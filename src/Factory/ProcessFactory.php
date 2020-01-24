<?php

namespace Millesime\Factory;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Process;

class ProcessFactory
{
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null) {
        $this->logger = $logger ?: new NullLogger;
    }

    public function __invoke($command, $workingDirectory = null)
    {
        return Process::fromShellCommandline(
            $command,
            $workingDirectory
        );
    }
}
