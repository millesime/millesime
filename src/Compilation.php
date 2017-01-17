<?php

namespace Methylbro\Compiler;

use Psr\Log\LoggerInterface;

class Compilation
{
    public function __construct(Project $project, LoggerInterface $logger = null)
    {
        if (!$logger) {
            $this->logger = new \Psr\Log\NullLogger();
        }

        if (!\Phar::canWrite()) {
            throw new Exception('You should update phar.readonly in your php.ini. http://php.net/phar.readonly');
        }

        $this->project = $project;
    }

    public function run(DistributionBuilder $distribution)
    {
        $project = $this->project;
        $config = $project->getConfig();

        $this->logger->notice("Run compilation for {$project->getSource()}");

        return array_reduce(
            $config['distrib'],

            function($result, $distrib) use ($project, $config, $distribution) {
                $result[$distrib['name']] = $distribution->build(array_merge(
                    $config,
                    [
                        'source' => $project->getSource(),
                        'dest' => $project->getDestination(),
                        'distrib' => $distrib,
                    ]
                ));
                return $result;
            }
        );
    }
}
