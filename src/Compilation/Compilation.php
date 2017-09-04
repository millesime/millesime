<?php

namespace Millesime\Compilation;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Compilation
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Project
     */
    private $project;

    /**
     * @param Project $project
     * @param LoggerInterface $logger
     * @throws \RuntimeException
     */
    public function __construct(Project $project, LoggerInterface $logger = null, $canWrite = ['Phar', 'canWrite'])
    {
        $this->project = $project;
        $this->logger = $logger ?: new NullLogger();

        if (!call_user_func($canWrite)) {
            throw new \RuntimeException('You should update phar.readonly in your php.ini. http://php.net/phar.readonly');
        }
    }

    /**
     * @param DistributionBuilder $builder
     * @return Array[\Phar]
     */
    public function run(DistributionBuilder $builder)
    {
        $config = $this->project->getConfig();
        $result = [];

        $this->logger->info("in {$this->project->getSource()}");

        foreach ($config['distrib'] as $distribution) {
            $result[$distribution['name']] = $this->buildDistribution($builder, $config, $distribution);
        }

        return $result;
    }

    /**
     * @param DistributionBuilder $builder
     * @param array $config
     * @param array $distribution
     * @return \Phar
     */
    private function buildDistribution(DistributionBuilder $builder, array $config, array $distribution)
    {
        return $builder->build(array_merge(
            $config,
            [
                'source' => $this->project->getSource(),
                'dest' => $this->project->getDestination(),
                'distrib' => $distribution,
            ]
        ));
    }
}
