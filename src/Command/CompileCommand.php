<?php

namespace Methylbro\Compiler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class CompileCommand extends Command
{
    protected function configure()
    {
		$phar = explode(DIRECTORY_SEPARATOR, getcwd());
		$phar = array_pop($phar);
		$phar = sprintf('%s.phar', $phar);

		$path = getcwd();

        $this
        	->setName('compile')
            ->setDescription('Compile your project in phar')

            ->addArgument('source', InputArgument::OPTIONAL, 'source of your app', $path)
            ->addArgument('dest', InputArgument::OPTIONAL, 'dest of your phar file', $phar)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	if (ini_get('phar.readonly')) {
    		$output->writeln('<error>You should update phar.readonly in your php.ini. http://php.net/phar.readonly</error>');
    		return;
    	}

    	$path = $input->getArgument('source');


    	if (file_exists($path.'/manifest.yml')) {
    		$output->writeln('Foud manifest.yml');
    		$distribs = Yaml::parse(file_get_contents($path.'/manifest.yml'));

    		foreach ($distribs as $distrib => $options) {
			    $output->writeln('build '.$distrib, OutputInterface::VERBOSITY_VERBOSE);
	    		$this->build($path, $distrib.'.phar', $output, $options['stub']);
    		}

    	} else {
	    	$pharName = $input->getArgument('dest');
	    	$this->build($path, $pharName, $output);
    	}

    }

    private function build($path, $pharName, $output, $stub=null)
    {

    	if (is_null($pharName)) {
    		$p = explode(DIRECTORY_SEPARATOR, $path);
    		$p = array_pop($p);
    		$pharName = sprintf('%s.phar', $p);
    		unset($p);
    	}

		$pharFile = $pharName;
		$pharName = basename($pharName);

		if (file_exists($pharFile)) {
		    $output->writeln('removes '.$pharFile, OutputInterface::VERBOSITY_VERBOSE);
			unlink($pharFile);
		}

	    $output->writeln('<info>compile '.$path.' into '. $pharFile.'</info>');

    	$phar = new \Phar($pharFile, 0, $pharName);
		
		//$phar->setSignatureAlgorithm(\Phar::SHA1);

		$phar->startBuffering();

		$finder = new Finder();
		$finder
			->files()
			->ignoreVCS(true)
			->in($path)
		;

	    $output->writeln('<info>Including '.count($finder).' php files</info>');

		$i=0;
		foreach ($finder as $fileInfo) {
		    $file = str_replace(__DIR__, '', $fileInfo->getRelativePathname());
		    $output->writeln('Add file: '.$file, OutputInterface::VERBOSITY_VERY_VERBOSE);
			$phar->addFile($fileInfo->getRelativePathname(), $file);
			$i++;
		}
		$output->writeln($i.' php files included.');

		if ($stub) {
			//if (!file_exists($path.'/'.$stub)) {}

		    $output->writeln('<info>Autoloading stub file</info>');

			$stub = <<<STUB
#!/usr/bin/env php
<?php
Phar::mapPhar('{$pharName}');
require 'phar://{$pharName}/{$stub}';
__HALT_COMPILER();
STUB;

			$phar->setStub($stub);
		}

		$phar->stopBuffering();

	    $output->writeln('created '.$pharFile, OutputInterface::VERBOSITY_VERBOSE);
    }
}
