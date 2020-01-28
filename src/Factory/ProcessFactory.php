<?php

namespace Millesime\Factory;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Process;

class ProcessFactory
{
    private $callback;
    private LoggerInterface $logger;

    /**
     * @param callable             $callback
     * @param LoggerInterface|null $logger
     */
    public function __construct(callable $callback, LoggerInterface $logger = null) {
        $this->callback = $callback;
        $this->logger = $logger ?: new NullLogger;
    }

    /**
     * Execute the given command
     *
     * @param string $command
     * @param string $workingDirectory
     *
     * @return Process
     */
    public function __invoke(string $command, string $workingDirectory = null) : Process
    {
        $this->logger->debug($command);

        return call_user_func($this->callback, $command, $workingDirectory);
    }
}
