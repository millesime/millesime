<?php

namespace Methylbro\Compiler;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Methylbro\Compiler\Compiler;

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
        if (file_exists($path.DIRECTORY_SEPARATOR.$manifest)) {
            $this->logger->notice('Using '.$manifest);
            
            $infos = json_decode(file_get_contents($path.DIRECTORY_SEPARATOR.$manifest));
            $distribs = $infos->distrib;

            foreach ($distribs as $distrib) {
                $this->logger->info('build '.$distrib->name);
                if (!property_exists($distrib, 'version')) $distrib->version = $infos->version;
                $distrib->app = $infos->name;

                $this->compiler->execute($path, $distrib->name.'.phar', $distrib);
            }

        } else {
            $this->logger->error($manifest.' was not found');

            $this->compiler->execute($path, $pharName);
        }
	}
}
