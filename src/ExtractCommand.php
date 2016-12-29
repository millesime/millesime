<?php

namespace Methylbro\Compiler;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;

class ExtractCommand extends Command
{
    protected function configure()
    {
        $this
        	->setName('extract')
            ->setDescription('Extract your project')

            ->addArgument('phar', InputArgument::REQUIRED, '')
            ->addArgument('path', InputArgument::OPTIONAL, '', getcwd())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
