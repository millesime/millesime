<?php

namespace Methylbro\Compiler;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Methylbro\Compiler\Compiler;
use Symfony\Component\Config\Definition\Processor;
use Methylbro\Compiler\Definition\CompilerConfiguration;

class Project
{
    private $compiler;
    private $logger;

    static public function guessPharName($path) {
        $phar = explode(DIRECTORY_SEPARATOR, $path);
        $phar = array_pop($phar);
        $phar = sprintf('%s.phar', $phar);

        return $phar;
    }

	public function __construct($compiler)
	{
        $this->compiler = $compiler;
        $this->logger = new \Psr\Log\NullLogger();
	}

    public function setLogger(LoggerInterface $logger) 
    {
        $this->logger = $logger;
    }

	public function compile($path, $pharName, $manifest)
	{
        $extra = array('version' => 'dev-master');

        if (file_exists($path.DIRECTORY_SEPARATOR.$manifest)) {
            $this->logger->notice('Using '.$manifest);
            
            $infos = (array) json_decode(file_get_contents($path.DIRECTORY_SEPARATOR.$manifest), true);

            $processor = new Processor();
            $configuration = new CompilerConfiguration();
            $processedConfiguration = $processor->processConfiguration(
                $configuration,
                [$extra, $infos]
            );

            foreach ($processedConfiguration['distrib'] as $distrib) {
                $this->logger->info('build '.$distrib['name']);

                $infos = $processedConfiguration;
                $infos['distrib'] = $distrib;

                $this->compiler->execute($path, $distrib['name'].'.phar', $infos);
            }

        } else {
            $this->logger->error($manifest.' was not found');

            $this->compiler->execute($path, $pharName);
        }
	}
}
