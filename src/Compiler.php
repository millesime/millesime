<?php

namespace Millesime;

use Millesime\Compilation\ProjectFactory;
use Millesime\Compilation\CompilationFactory;
use Millesime\Compilation\DistributionBuilder;

class Compiler
{
    /**
     * @var ProjectFactory
     */
    private $projectFactory;

    /**
     * @var CompilationFactory
     */
    private $compilationFactory;

    /**
     * @var DistributionBuilder
     */
    private $distributionBuilder;

    /**
     * @param ProjectFactory $projectFactory
     * @param CompilationFactory $compilationFactory
     * @param DistributionBuilder $distributionBuilder
     */
    public function __construct(ProjectFactory $projectFactory, CompilationFactory $compilationFactory, DistributionBuilder $distributionBuilder)
    {
        $this->projectFactory = $projectFactory;
        $this->compilationFactory = $compilationFactory;
        $this->distributionBuilder = $distributionBuilder;
    }

    /**
     * @param string $source
     * @param string $dest
     * @param string $manifest
     * @return Array
     */
    public function execute($source, $dest, $manifest)
    {
        $project = $this->projectFactory->create($source, $dest, $manifest);
        $compilation = $this->compilationFactory->create($project);

        return $compilation->run($this->distributionBuilder);
    }
}
