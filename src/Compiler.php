<?php

namespace Methylbro\Compiler;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;

class Compiler implements LoggerAwareInterface
{
	private $logger;

    public function __construct()
    {
    	$this->logger = new \Psr\Log\NullLogger();
    }

    public function setLogger(LoggerInterface $logger) 
    {
    	$this->logger = $logger;
    }

    public function execute($path, $pharName=null, $infos=null)
    {
        if (!\Phar::canWrite()) {
        	$this->logger->critical('You should update phar.readonly in your php.ini. http://php.net/phar.readonly');
            return;
        }

        $stub = null;
        if ($infos) {
            $stub = $infos['distrib']['stub'];
        }

        if (is_null($pharName)) {
        	$pharName = Project::guessPharName($path);
        }

        $pharFile = $pharName;
        $pharName = basename($pharName);

        if (file_exists($pharFile)) {
        	$this->logger->info('Removes existing '.$pharFile);
            unlink($pharFile);
        }

        $this->logger->warning('Compile '.$path.' into '. $pharFile);

        $phar = new \Phar($pharFile, 0, $pharName);
        
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finder = new Finder();
        $finder
            ->files()
            ->ignoreVCS(true)
            ->in($path)
        ;

        $this->logger->warning('Including '.count($finder).' files');

        $i=0;
        foreach ($finder as $fileInfo) {
            $file = str_replace($path, '', $fileInfo->getRelativePathname());
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
        $this->logger->notice($i.' files included');

        if ($stub) {
            //if (!file_exists($path.'/'.$stub)) {}

            $this->logger->warning('Setting up the loader or the bootstrap stub of the Phar archive');

			$code =            
<<<STUB
<?php
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

        $phar->stopBuffering();

        $this->logger->info('created '.$pharFile);
    }
}
