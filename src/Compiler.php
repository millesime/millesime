<?php

namespace Millesime\Compiler;

class Compiler
{
    public function __construct($projectFactory, $compilationFactory, $distributionBuilder)
    {
    	$this->projectFactory = $projectFactory;
    	$this->compilationFactory = $compilationFactory;
    	$this->distributionBuilder = $distributionBuilder;
   	}

    public function execute($source, $dest, $manifest)
    {
    	$project = $this->projectFactory->create($source, $dest, $manifest);
    	$compilation = $this->compilationFactory->create($project);

    	return $compilation->run($this->distributionBuilder);
    }
}
