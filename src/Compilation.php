<?php

namespace Millesime\Compiler;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Compilation
{
    private $logger;
    private $project;

    public function __construct(Project $project, LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();

        if (!\Phar::canWrite()) {
            throw new \Exception('You should update phar.readonly in your php.ini. http://php.net/phar.readonly');
        }

        $this->project = $project;
    }

    public function run(DistributionBuilder $builder)
    {
        $config = $this->project->getConfig();

        $this->logger->info("Run compilation for {$this->project->getSource()}");

        $result = [];

        foreach ($config['distrib'] as $distribution) {
            $result[$distribution['name']] = $this->buildDistribution($builder, $config, $distribution);
        }
    }

    private function buildDistribution(DistributionBuilder $builder, array $config, array $distribution)
    {
        $this->logger->info("Building ".$distribution['name'].".phar");

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
