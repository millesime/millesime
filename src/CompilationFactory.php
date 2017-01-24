<?php

namespace Millesime\Compiler;

class CompilationFactory
{
    private $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function create($project, $logger = null)
    {
        return new $this->class($project, $logger);
    }
}
