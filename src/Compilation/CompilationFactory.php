<?php

namespace Millesime\Compilation;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class CompilationFactory
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string $class
     * @param LoggerInterface $logger
     */
    public function __construct($class = Compilation::class, LoggerInterface $logger = null)
    {
        $this->class = $class;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param Project $project
     * @return Compilation
     */
    public function create(Project $project)
    {
        return new $this->class($project, $this->logger);
    }
}
