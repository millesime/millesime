<?php

namespace Methylbro\Compiler;

use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Processor;
use Methylbro\Compiler\Definition\CompilerConfiguration;
use Symfony\Component\Finder\Finder;

class Project
{
    private $source;
    private $manifest;
    private $logger;

	public function __construct($source, $dest =null, $manifest = 'compiler.json')
	{
        $this->source = $source;
        $this->dest = $dest ? $dest : $source;
        $this->manifest = $manifest;
        $this->logger = new \Psr\Log\NullLogger();
	}

    public function setLogger(LoggerInterface $logger) 
    {
        $this->logger = $logger;
    }

	public function compile()
	{
        /*
         * booting compiler
         */
        if (!\Phar::canWrite()) {
            $this->logger->critical('You should update phar.readonly in your php.ini. http://php.net/phar.readonly');
            return;
        }

        $manifest = realpath($this->source).DIRECTORY_SEPARATOR.$this->manifest;
        if (!file_exists($manifest)) {
            $this->logger->error($manifest.' was not found');
            return;
        }
        $this->logger->notice('Using '.$this->manifest);

        /*
         * resolve project configuration
         */            
        $extra = ['version' => $this->detectVersion($this->source)];
        $infos = (array) json_decode(file_get_contents($manifest), true);

        $processor = new Processor();
        $configuration = new CompilerConfiguration();
        $processedConfiguration = $processor->processConfiguration(
            $configuration,
            [$extra, $infos]
        );

        /*
         * compile distributions
         */
        foreach ($processedConfiguration['distrib'] as $distrib) {
            $this->logger->info('build '.$distrib['name']);

            $infos = $processedConfiguration;
            $infos['distrib'] = $distrib;

            $this->dist($infos);
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

    private function dist($infos)
    {
        $stub = null;
        if ($infos) {
            $stub = $infos['distrib']['stub'];
        }

        $source = realpath($this->source);
        $pharName = $infos['distrib']['name'].'.phar';
        $pharFile = realpath($this->dest).DIRECTORY_SEPARATOR.$pharName;

        if (file_exists($pharFile)) {
            $this->logger->info('Removes existing '.$pharFile);
            unlink($pharFile);
        }

        $this->logger->warning('Compile '.$this->source.' into '. $pharName);

        $finder = new Finder();
        $finder
            ->files()
            ->ignoreVCS(true)
            ->in($source)
        ;

        $this->logger->notice('Including '.count($finder).' files');


        $phar = new \Phar($pharFile);

        /*
         * configure phar options
         */

        //$phar->setSignatureAlgorithm(\Phar::SHA1);

        if ($stub) {
            //if (!file_exists($path.'/'.$stub)) {}

            $this->logger->warning('Setting up the loader or the bootstrap stub of the Phar archive');

            $code =            
<<<STUB
<?php
Phar::interceptFileFuncs();
Phar::mapPhar('{$pharName}');
require 'phar://{$pharName}/{$stub}';
__HALT_COMPILER();
STUB;
            if ($infos['distrib']['autoexec']) {
                $code = '#!/usr/bin/env php'.PHP_EOL.$code;
            }

            $phar->setStub($code);

            $this->logger->notice($stub.' will be used as stub file');
        }


        /**
         * Buffering phar content
         */
        $phar->startBuffering();

        $this->logger->debug('start buffering');


        $i=0;
        foreach ($finder as $fileInfo) {
            $file = str_replace($source, '', $fileInfo->getRelativePathname());
            $content = file_get_contents($file);

            if ($infos['distrib']['autoexec'] && $file==$stub) {
                $content = str_replace('#!/usr/bin/env php'.PHP_EOL, null, $content);
            }

            if ($infos) {
                $content = str_replace('@name@', $infos['name'], $content);
                $content = str_replace('@version@', $infos['version'], $content);
                $content = str_replace('@distrib@', $infos['distrib']['name'], $content);
            }

            $this->logger->debug('Add file: '.$file);
            $phar->addFromString($fileInfo->getRelativePathname(), $content);
            $i++;
        }
        $this->logger->warning($i.' files included');


        $phar->stopBuffering();

        $this->logger->info('created '.$pharFile);
    }
}
