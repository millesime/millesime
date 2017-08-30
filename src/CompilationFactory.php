<?php

namespace Millesime\Compiler;

class CompilationFactory
{
    private $class;

    public function __construct($class, $logger)
    {
        $this->class = $class;
        $this->logger = $logger;
    }

    public function create($project)
    {
        return new $this->class($project, $this->logger);
    }
}
