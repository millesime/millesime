<?php

namespace Methylbro\Compiler;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;

class CompileCommand extends Command
{
    protected function configure()
    {
		$phar = explode(DIRECTORY_SEPARATOR, getcwd());
		$phar = array_pop($phar);
		$phar = sprintf('%s.phar', $phar);

        $this
        	->setName('compile')
            ->setDescription('Compile your project in phar')

            ->addArgument('phar', InputArgument::OPTIONAL, '', $phar)
            ->addArgument('path', InputArgument::OPTIONAL, '', getcwd())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$pharName = $input->getArgument('phar');
    	$path = $input->getArgument('path');

    	if (is_null($pharName)) {
    		$p = explode(DIRECTORY_SEPARATOR, $path);
    		$p = array_pop($p);
    		$pharName = sprintf('%s.phar', $p);
    		unset($p);
    	}

		$pharFile = $path . '/' . $pharName;

		if (file_exists($pharFile)) {
		    $output->writeln('removes '.$pharFile, OutputInterface::VERBOSITY_VERBOSE);
			unlink($pharFile);
		}


	    $output->writeln('<info>create '.$pharFile.'</info>');

    	$phar = new \Phar($pharFile, 0, $pharName);
		
		//$phar->setSignatureAlgorithm(\Phar::SHA1);
		
		$phar->startBuffering();

		$finder = new Finder();
		$finder
			->files()
			->name('*.php')
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

	    $output->writeln('<info>Autoloading stub file</info>');

$stub = <<<STUB
#!/usr/bin/env php
<?php
Phar::mapPhar('{$pharName}');
require 'phar://{$pharName}/builder.php';
__HALT_COMPILER();
STUB;

		$phar->setStub($stub);
		$phar->stopBuffering();

	    $output->writeln('created '.$pharFile, OutputInterface::VERBOSITY_VERBOSE);
    }
}
