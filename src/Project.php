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
        $extra = ['version' => $this->detectVersion($path)];

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

    private function detectVersion($path)
    {
        $version = 'dev-master';
        $gitdir = $path.DIRECTORY_SEPARATOR.'.git';
        
        if (file_exists($gitdir)) {
            $c = "git --git-dir={$gitdir} show -q";
            $show = shell_exec($c);
            $commit = explode(PHP_EOL, $show)[0];
            $commit = substr($commit, 7);

            if ($commit) {
                $c = "git --git-dir={$gitdir} tag --contains {$commit}";
                $tags = shell_exec($c);
                $tags = explode(PHP_EOL, $tags);
                $tag = $tags[0];

                if ($tag) {
                    $version = $tag;
                } else {
                    $c = "git --git-dir={$gitdir} branch";
                    $branch = shell_exec($c);
                    $branch = explode(PHP_EOL, $branch);
                    foreach ($branch as $b) {
                        if (substr($b, 0, 1)==='*') {
                            $branch = trim($b, '* ');
                        }
                    }
                    $version = 'dev-'.$branch;
                }
            }
        }

        return $version;
    }
}
